<!-- 以下是一個簡單的示例，展示如何使用AJAX技術編輯與在網頁上更新table數據。
步驟1：創建一個網頁，顯示需要編輯的table數據。 -->
<!-- <!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>編輯table數據</title>
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
      <td>小明</td>
      <td>18</td>
      <td>男</td>
      <td>北京</td>
    </tr>
    <tr>
      <td>小花</td>
      <td>20</td>
      <td>女</td>
      <td>上海</td>
    </tr>
  </table>
</body>
</html> -->

<!-- 步驟2：將table數據轉換為可編輯的表單。 -->
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
    .edit{
      display: none;
      width: 100%;
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
      <th>操作</th>
    </tr>
    <tr>
      <td>小明</td>
      <td>18</td>
      <td>男</td>
      <td>北京</td>
      <td><button onclick="editRow(this)">編輯</button></td>
    </tr>
    <tr>
      <td>小花</td>
      <td>20</td>
      <td>女</td>
      <td>上海</td>
      <td><button onclick="editRow(this)">編輯</button></td>
    </tr>
  </table>

  <form id="editForm" class="edit">
    <input type="text" id="name">
    <input type="text" id="age">
    <input type="text" id="gender">
    <input type="text" id="address">
    <button type="button" onclick="saveRow()">保存</button>
    <input type="hidden" id="rowId">
  </form>

  <script>
    function editRow(button){
      var row = $(button).closest("tr");
      var name = row.find("td:eq(0)").text();
      var age = row.find("td:eq(1)").text();
      var gender = row.find("td:eq(2)").text();
      var address = row.find("td:eq(3)").text();

      $("#name").val(name);
      $("#age").val(age);
      $("#gender").val(gender);
      $("#address").val(address);

      $("#rowId").val(row.index());

      $("tr").not(row).hide();
      $(".edit").show();
    }

    function saveRow(){
      var index = $("#rowId").val();
      var name = $("#name").val();
      var age = $("#age").val();
      var gender = $("#gender").val();
      var address = $("#address").val();

      $("tr").not(":first").eq(index).find("td:eq(0)").text(name);
      $("tr").not(":first").eq(index).find("td:eq(1)").text(age);
      $("tr").not(":first").eq(index).find("td:eq(2)").text(gender);
      $("tr").not(":first").eq(index).find("td:eq(3)").text(address);

      $(".edit").hide();
      $("tr").not(":first").show();

      //使用AJAX技術，儲存數據到服務器端
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
    }
  </script>
</body>
</html>

<!-- 步驟3：在JavaScript中實現編輯與儲存table數據的功能，同時使用AJAX技術將數據傳送到服務器端進行保存。 -->
<!-- 在編輯table數據時，我們需要將每一行數據轉換為表單，用戶可以在表單中進行編輯。這裡使用jQuery的closest()函數來找到被點擊的按鈕所在的行，然後使用find()函數找到該行中各個列的值。編輯完成後，我們需要使用ajax()函數將修改後的數據傳送到服務器端進行儲存。 -->
<!-- 在儲存table數據時，我們需要找到被修改的行的索引，然後使用eq()函數找到這一行。接著，我們可以使用find()函數找到這一行中需要修改的列，並使用text()函數將其值修改為表單中的值。最後，使用ajax()函數將修改後的數據傳送到服務器端進行儲存。 -->
