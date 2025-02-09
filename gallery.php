<!doctype html>



<?php

// open this directory
$myDirectory = opendir("uploads");

// get each entry
while($entryName = readdir($myDirectory)) {
    $dirArray[] = $entryName;
}

// close directory
closedir($myDirectory);

//	count elements in array
$indexCount	= count($dirArray);

?>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Include Fotorama -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="image.css">
    <script src="script2.js"></script>
</head>

<h4>Image Title</h4>
<p>Image Description</p>
<div class="fotorama-container">
    <div class="fotorama" data-nav="thumbs" data-loop="true">
            <?php
            // loop through the array of files and print them all in a list
            for($index=0; $index < $indexCount; $index++) {
                $extension = substr($dirArray[$index], -3);
                if ($extension == 'png' || $extension =='jpg' || $extension =='jpge'){
                    echo '<img src="uploads/' . $dirArray[$index] . '" alt="Image" />';
                }
            }
            ?>
    </div>
</div>
<br/>
<div class="button-container">
    <button id="fb-share-button" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></button>
    <button id="twitter-share-button" aria-label="Share on Twitter"><i class="fab fa-twitter"></i></button>
    <button id="linkedin-share-button" aria-label="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></button>
</div>
































</body>
</html>