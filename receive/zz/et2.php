<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>編輯table數據</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <style>
    table{
      border-collapse: collapse;
      width: 100%;
    }
    table th,td{
      border: 1px solid black;
      padding: 10px;
      text-align: center;
    }
    td input{
      width: 100%;
      border: none;
      box-sizing: border-box;
      text-align: center;
    }
    td input:focus{
      outline: none;
    }
    .edit{
      display: none;
    }
  </style>
</head>
<body>
  <table id="myTable" border="1">
    <tr>
      <th>姓名</th>
      <th>年齡</th>
      <th>性別</th>
      <th>地址</th>
    </tr>
    <tr>
      <td class="editable" data-field="name">小明</td>
      <td class="editable" data-field="age">18</td>
      <td class="editable" data-field="gender">男</td>
      <td class="editable" data-field="address">北京</td>
    </tr>
    <tr>
      <td class="editable" data-field="name">小花</td>
      <td class="editable" data-field="age">20</td>
      <td class="editable" data-field="gender">女</td>
      <td class="editable" data-field="address">上海</td>
    </tr>
  </table>

  <div id="editRow" class="edit">
    <button id="saveRow">保存</button>
  </div>

  <script>
    //編輯table中的行
    $("table").on("dblclick","td.editable",function(){
      var value = $(this).text();
      var field = $(this).attr("data-field");
      var input = $('<input type="text">').val(value);
      $(this).html(input);
      $(input).focus();
      $(input).on("blur",function(){
        var newValue = $(this).val();
        $("table td[data-field='"+field+"']").each(function(){
          $(this).text(newValue);
        });
      });
    });
    
    //在編輯模式下，禁止點擊其他行
    $("table").on("click","td.editable input",function(event){
      event.stopPropagation();
    });

    //保存行數據，並使用 AJAX 技術將數據傳送到服務器儲存
    $("#saveRow").click(function(){
      var row = $(this).closest("tr");
      var name = row.find("td[data-field='name']").text();
      var age = row.find("td[data-field='age']").text();
      var gender = row.find("td[data-field='gender']").text();
      var address = row.find("td[data-field='address']").text();

      $.ajax({
        type: "POST",
        url: "saveData.php",
        data: { 
          name: name, 
          age: age, 
          gender: gender, 
          address: address 
        }
      }).done(function( msg ) {
        alert( "數據已保存: " + msg );
      });

      $(".editable input").blur();
    });

    //點擊表格中任意位置，取消編輯模式
    $("table").on("click", function(){
      $(".editable input").blur();
    });

    //禁止表格中的右鍵點擊，避免一些意外的錯誤
    $('table').on('contextmenu', function(event) {
      event.preventDefault();
      event.stopPropagation();
      return false;
    });
  </script>
</body>
</html>