<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Door Lock - History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="icon" href="assets/img/door-handle.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/2406124756.js" crossorigin="anonymous"></script>
    <style>
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
    <script>
        // Function to update history table content
        function updateHistoryTable() {
            $.ajax({
                url: "lihatHistory.php",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    var historyTableBody = $("#history-table-body");

                    // Clear existing rows
                    historyTableBody.empty();

                    // Generate new rows
                    var no = 1;
                    data.forEach(function(history) {
                        var row = "<tr>" +
                            "<th>" + no + "</th>" +
                            "<td>" + history.status_pintu + "</td>" +
                            "<td>" + history.tanggal + "</td>" +
                            "</tr>";
                        historyTableBody.append(row);
                        no++;
                    });
                },
                error: function(error) {
                    console.error("Error updating history table:", error);
                }
            });
        }

        // Update history table every 5 seconds (adjust the interval as needed)
        setInterval(updateHistoryTable, 1000); // 5000 milliseconds = 5 seconds
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
    </div>
    <div class="container bg-light rounded p-4 mt-4 col-4 col-md-8 col-sm-10">
        <table class="table table-striped table-dark text-center">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tanggal - Waktu</th>
                </tr>
            </thead>            
            <tbody id="history-table-body"></tbody>
        </table>
    </div>
    </div>
</body>

</html>