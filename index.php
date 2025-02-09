<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
<div class="container">
    <div class="contr">
        <img src="img/logo.png" class="center">
        <h2 style="text-align: center;">image uploader.</h2>
    </div>

    <form method='post' action='' enctype='multipart/form-data' class="file-uploader">
        <div class="file-uploader__message-area">
            <p>Select a file to upload</p>
        </div>
        <div class="file-chooser">
            <input class="file-chooser__input" type="file" name="file[]" id="file" multiple><br/><br/>
        </div>

        <?php
        if(isset($_POST['submit'])){
            $countfiles = count($_FILES['file']['name']);
            $totalFileUploaded = 0;
            for($i=0;$i<$countfiles;$i++){
                $filename = $_FILES['file']['name'][$i];
                ## Location
                $location = "./uploads/".$filename;
                $extension = pathinfo($location,PATHINFO_EXTENSION);
                $extension = strtolower($extension);
                ## File upload allowed extensions
                $valid_extensions = array("jpg","jpeg","png");
                $response = 0;
                ## Check file extension
                if(in_array(strtolower($extension), $valid_extensions)) {
                    ## Upload file
                    if(move_uploaded_file($_FILES['file']['tmp_name'][$i],$location)){

                        echo "file name : ".$filename."<br/>";
                    }
                }
            }
        }
        ?>
        <input class="file-uploader__submit-button" type='submit' name='submit' value='Upload'>
        <input class="file-uploader__submit-button" onclick="location.href='gallery.php';" name='Gallery' value='Gallery' style="text-align: center;">
    </form>


</div>
</body>

