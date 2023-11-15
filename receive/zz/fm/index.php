<!DOCTYPE html>
<html lang="en">

<head>
  <title></title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,300,0,0" />
    <link rel="stylesheet" href="../../libs/bootstrap/css/bootstrap.min.css">
  <!-- <link href="./src/bootstrap.css" rel="stylesheet"> -->

  <style>
    #navbar::-webkit-scrollbar {
      display: none;
    }

    :root {
      --header-hight: 52px;
      --navbar-width: 80px;
    }

    iframe {
      margin: 0;
      padding: 0;

      /* header hight => 52px */
      height: calc(100vh - var(--header-hight));

      /* navbar width => 80px */
      width: calc(100vw - var(--navbar-width));
      margin-left: var(--navbar-width);
    }

    button {
      text-align: left;
      display: block;
      width: 100%;
      border: none;
      background-color: inherit;
      color: inherit;
      white-space: nowrap;
      /* height: 60px; */
      border-left: 3px solid rgb(33, 37, 41);
    }

    span {
      white-space: nowrap;
      text-align: center;
    }

    #navbar {
      background-color: rgb(33, 37, 41);
      width: 80px;
      overflow-x: hidden;
      overflow-y: auto;
      color: white;
      transition: width 0.3s ease;
      z-index: 97;
    }

    #navbar:hover {
      width: 240px;
    }

    .navBody {
      margin-top: 50px;
      color: #afa5d9;
    }

    button:hover {
      color: rgb(247, 246, 251);
      border-left: 3px solid #afa5d9;
    }

    .active {
      border-left: 3px solid #afa5d9;
      color: white;
    }

    .header {
      color: white;
      background-color: rgb(33, 37, 41);
      font-size: 25px;
      font-weight: 500;
    }

    .icon {
      /* margin-top: 5px; */
      margin-left: 15px;
      margin-right: 30px;
      font-size: 35px;
    }

    .node {
      margin: 0px 0px 10px 0px;
      padding: 15px 0px;
    }

    .download {
      position: fixed;
      top: 5px;
      right: 10px;

      padding: 5px 10px 5px 10px;
      color: whitesmoke;
      border-radius: 5px;
    }

    .download:hover {
      background-color: rgb(37, 185, 37);
      color: rgb(255, 255, 255);
    }

    #sign {
      margin-left: var(--navbar-width);
    }
  </style>

    <script src="../../libs/jquery/jquery.min.js" referrerpolicy="no-referrer"></script>
  <script>

    $(document).ready(async function () {
      let name = [];
      let url = {};
      let icons = [];
      let topic = "";
      let page_status = [];
      let title = [];
      let sidebar_style = {};
      let active_item_index = 0;
      let iframe = $('#iframe');
      let sign = $('#sign');
      let current_iframe = "";

      $(window).resize(function () {
        $('#iframe').attr('src', current_iframe)
      });

      // get setting info
      await $.getJSON('Node_Setting.txt').then((result) => {
        try {
          topic = result["topic"];
          sidebar_style = result["sidebar_style"];

          let omit_obj = ["topic", "預設頁面", "sidebar_style"];
          let filtered_data = Object.fromEntries(Object.entries(result)
            .filter(([key, value]) => !omit_obj.includes(key))
            .filter(([key, value]) => key.substring(0, 5) != "HIDE_")
          );

          name = Object.keys(filtered_data);
          active_item_index = name.indexOf(result["預設頁面"]);
          current_iframe = filtered_data[result["預設頁面"]].網址;

          let key = "";
          for (let i in name) {
            key = name[i];
            url[key] = filtered_data[name[i]]["網址"]
            icons.push(filtered_data[name[i]]["圖示"]);
            page_status.push(filtered_data[name[i]]["另開分頁"]);
            title.push(filtered_data[name[i]]["說明"]);
          }
        }
        catch (e) {
          console.log("falied to get info", e);
        }
      });

      // switch node status
      const active = (id) => {
        console.log(id);
        for (let i in Object.keys(url)) {
          if (Object.keys(url)[i] == id) {
            $('#btn_' + id).addClass("active");
          }
          else {
            $('#btn_' + Object.keys(url)[i]).removeClass("active");
          }
        }
      };

      // append node button on SideBar
      for (let i = 0; i < name.length; i++) {
        $('.navBody').append(`
          <button type="button" id="btn_${name[i]}" class="" ${title[i] ? `title="${title[i]}"` : ""}
          style="margin: 5px 0px;padding: ${sidebar_style["paddingY"]}px 0px">
            <div class="d-flex align-items-center h-100 p-0 m-0">
              <span class="material-symbols-rounded icon">
                ${icons[i]}
              </span>
              ${name[i]}
            </div>
          </button>
          `
        );
      }

      // initialized
      const initialized = () => {
        let active_item = Object.keys(url)[active_item_index];
        sign.hide();
        active(active_item);
        $('#iframe').attr('src', url[active_item]);
        $('#header').text(topic);
        $("title").text(topic);

        if (sidebar_style) {
          $("#navbar").hover(function (e) {
            $(this).css("width", e.type === "mouseenter" ? sidebar_style["hover_width"] : "80");
          })
        }
      }
      initialized();

      // node button click event => switch iframe src OR open new tab
      $('.navBody').on('click', 'button', function () {
        let id = $(this).attr('id').replace('btn_', '');
        if (page_status[name.indexOf(id)] == true) {
          window.open(url[id], height = 500, width = 500);
          // iframe.hide();
          // sign.html("導向至 <a href='" + url[id] + "' target='_blank'>" + url[id] + "</a>");
          // sign.show();
        } else {
          active(id);
          current_iframe = url[id]
          iframe.attr('src', url[id]);
          sign.hide();
          iframe.show();
        }
      });
    });
  </script>

</head>

<body>
  <div class="p-1 d-flex justify-content-center w-100 header">
    <span class="m-0" id="header"></span>
  </div>
  <div class="fixed-top h-100" id="navbar">
    <div class="navBody">
    </div>
  </div>

  <div id="sign" class=""></div>
  <iframe id="iframe" src="" frameborder="0"></iframe>
  <a href="http://10.55.28.145:3000/web/Frame_Module/Frame_Module.zip" class="download">Download</a>
</body>

</html>