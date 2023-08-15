<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Door Lock Toggle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"></link>
    <link rel="stylesheet" href="assets/css/button.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <div class="p-4">
        <div class="container">
            <div class="toggle-btn">
                <div class="icon">
                    <i class='bx bx-lock'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for Sliding Toggle -->
    <script>
        const toggleBtn =document.querySelector(".toggle-btn"),
        lockIcon = document.querySelector(".icon i");

        toggleBtn.addEventListener("click", () => {
            toggleBtn.classList.toggle("active");

            if(toggleBtn.classList.contains("active")){
                return lockIcon.classList.replace("bx-lock","bxs-lock-open");
            }
            lockIcon.classList.replace("bxs-lock-open","bx-lock");
        })
    </script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/2406124756.js" crossorigin="anonymous"></script>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>