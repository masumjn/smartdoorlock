<?php
// Include Database Configuration
include 'koneksi.php';

// Koneksi ke Database
$koneksi = mysqli_connect($hostname, $username, $password, $database);

// Read Data dari Tabel tabel_udara
$sql = mysqli_query($koneksi, "select * from tabel_relay");

// Read Data Terbaru
$data = mysqli_fetch_array($sql);
$status = $data['status'];
// echo $status;
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smart Door Lock - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
  <link rel="icon" href="assets/img/door-handle.png">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    /* span {
      display: none;
    } */
    .video-containerku video {
      position: absolute;
      object-fit: cover;
      border-radius: 10px;
      height: 100%;
      background-color: #5b6374;
    }

    .fa-45deg {
      transform: rotate(45deg);
    }

    .link-secondary {
      background-color: none;
      padding: 15px;
      border-radius: 5px;
      transition: background-color ease-in 0.1s;
    }

    .link-secondary:hover {
      background-color: lightgray;
      padding: 15px;
      border-radius: 5px;
      transition: background-color ease-in 0.2s;
    }
  </style>

  <script type="text/javascript">
    // Baca Status WiFi dari Database
    $(document).ready(function() {
      var wifiStatus;
      setInterval(function() {
        $("#wifi").load("cekwifi.php", function(response) {
          wifiStatus = response; // Store the value in a variable
          console.log(wifiStatus); // Output the value in the browser console
        });
      }, 1000);
    });

    // Global Variables
    var relayStatus;
    // Baca Status relay dari Database
    $(document).ready(function() {
      // var relayStatus;
      setInterval(function() {
        $("#relay").load("cekrelay.php", function(response) {
          relayStatus = response; // Store the value in a variable
          console.log(relayStatus); // Output the value in the browser console
        });
      }, 1000);
    });
  </script>


</head>

<body class="">
  <div class="p-4">
    <div class="container bg-light text-center rounded p-4 mt-4 col-4 col-md-6 col-sm-10">
      <div class="row d-flex align-items-center justify-content-around">
        <div class="col">
          <a href="index.php" class="link-secondary">
            <i class="fa-solid fa-door-open fa-2xl"></i>
          </a>
        </div>
        <div class="col-7">
          <h1>SMART DOOR LOCK</h1>
        </div>
        <div class="col">
          <a href="history.php" class="link-secondary">
            <i class="fa-solid fa-file fa-2xl"></i>
          </a>
        </div>
      </div>
    </div>
    <div class="container bg-light rounded p-4 mt-4 col-4 col-md-8 col-sm-10">
      <div class="card text-bg-secondary mb-3 rounded h-100 cek-klik video-containerku" id="background">
        <!-- Background Video -->
        <video autoplay muted class="col-12" style="z-index: 1; overflow: hidden; background-position: right center;" id="video-background">
          <source src="assets/video/SolenoidBackward.mp4" type="video/mp4" />
        </video>
        <div class="card-body" style="z-index: 2;">
          <div class="col-6 m-2 p-2 mb-3">
            <h1 class="card-title"> <strong> 100% </strong></h1>
            <div class="d-flex">
              <i class="fa-solid fa-plug p-1 mt-1 fa-45deg"></i>
              <p class="card-text p-1">power</p>
            </div>
          </div>
          <div class="col-6 m-2 p-2 mb-5">
            <h1 class="card-title">
              <strong>
                <span id="wifi">...</span>
              </strong>
            </h1>
            <div class="d-flex">
              <i class="fa-solid fa-wifi p-1 mt-1"></i>
              <p class="card-text p-1">wifi</p>
            </div>
          </div>
          <div class="container m-2 p-0" id="tombol">
            <!-- <div class="toggle-btn <?php if ($status == 1) echo "active"; ?>"> -->
            <div class="toggle-btn">
              <div class="prev icons col-3" id="icons-prev">
                <i class='bx bx-lock'></i>
              </div>
              <div class="icon col-3">
                <!-- <i class='bx bx-lock <?php if ($status == 1) echo "bxs-lock-open"; ?>'> -->
                <i class='bx bx-lock'>
                  <span id="relay" value="" hidden></span>
                </i>

              </div>
              <div class="next icons col-3" id="icons-next">
                <i class='bx bxs-lock-open'></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Script for Sliding Toggle & Kirim Data ke Database-->
  <script>
    const toggleBtn = document.querySelector(".toggle-btn"),
      lockIcon = document.querySelector(".icon i"),
      prevIcon = document.querySelector(".toggle-btn .prev"),
      nextIcon = document.querySelector(".toggle-btn .next");

    var relayS = relayStatus;

    var video = document.getElementById("video-background");

    // if (relayS === '0') {
    //   if (toggleBtn.classList.contains("active")) {
    //     toggleBtn.classList.toggle("active");
    //     loadAndRun();
    //   }
    // }
    // Make style when database show 0
    const isActive = toggleBtn.classList.contains("active");
    if (isActive && ($relayStatus === '0')) {
      var toggleBtn1 = document.getElementById("toggleBtn");
      toggleBtn1.classList.toggle("active");
      loadAndRun();
    }

    if (toggleBtn.classList.contains("active")) {
      prevIcon.style.transform = "translateX(19%)";
      prevIcon.style.scale = "0.8";

      nextIcon.style.transform = "translateX(calc(100% + 80%))";
      nextIcon.style.scale = "0.5";

      lockIcon.classList.replace("bx-lock", "bxs-lock-open");

      relayS = 1;
    } else {
      prevIcon.style.transform = "translateX(10%)";
      prevIcon.style.scale = "0.5";

      nextIcon.style.transform = "translateX(109%)";
      nextIcon.style.scale = "0.8";

      lockIcon.classList.replace("bxs-lock-open", "bx-lock");

      relayS = 0;
    }
    // Fungsi untuk mengganti nilai relay di database ketika diklik
    toggleBtn.addEventListener("click", () => {
      toggleBtn.classList.toggle("active");

      if (toggleBtn.classList.contains("active")) {
        prevIcon.style.transform = "translateX(19%)";
        prevIcon.style.scale = "0.8";

        nextIcon.style.transform = "translateX(calc(100% + 80%))";
        nextIcon.style.scale = "0.5";

        lockIcon.classList.replace("bx-lock", "bxs-lock-open");

        video.style.backgroundColor = '#5b6374';
        video.src = 'assets/video/SolenoidForward.mp4';
        video.currentTime = 0; // Start playing from the beginning
        video.playbackRate = 1; // Play forward
        video.play();
        video.style.backgroundColor = '#5f6574';

        relayS = 1;
      } else {
        prevIcon.style.transform = "translateX(10%)";
        prevIcon.style.scale = "0.5";

        nextIcon.style.transform = "translateX(109%)";
        nextIcon.style.scale = "0.8";

        lockIcon.classList.replace("bxs-lock-open", "bx-lock");

        video.style.backgroundColor = '#5b6374';
        video.src = 'assets/video/SolenoidBackward.mp4';
        video.currentTime = 0; // Start playing from the beginning
        video.playbackRate = 1; // Play forward
        video.play();
        video.style.backgroundColor = '#5f6574';

        relayS = 0;
      }

      // Update relay value
      document.getElementById('relay').innerHTML = relayS;

      // Ajax untuk Mengubah status Relay di Database
      var relayStat = relayS; // Assign the value of relayS to relayStat

      console.log(relayStat);

      var xmlhttp = new XMLHttpRequest();

      // xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        // Ambil Respon dari Web
        document.getElementById('relay').innerHTML = xmlhttp.responseText;
      }
      // };

      // Execute file PHP untuk merubah nilai di Database
      xmlhttp.open("GET", "triggerRelay.php?status=" + relayStat, true);

      // Kirim ke Database
      xmlhttp.send();
    });

    function geserKiri() {
      toggleBtn.classList.toggle("active");

      prevIcon.style.transform = "translateX(10%)";
      prevIcon.style.scale = "0.5";

      nextIcon.style.transform = "translateX(109%)";
      nextIcon.style.scale = "0.8";

      lockIcon.classList.replace("bxs-lock-open", "bx-lock");

      relayS = 0;
    }
    // rencana buat ganti style ke kiri, kalo nilai 1
    // if ((relayStatus === 0) && (toggleBtn.classList.contains("active"))) {
    //   geserKiri();
    // }
  </script>

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/2406124756.js" crossorigin="anonymous"></script>
  <!-- JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>