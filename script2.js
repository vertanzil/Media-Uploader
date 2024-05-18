$(document).ready(function() {
    // Initiate Fotorama
    var $fotoramaDiv = $('.fotorama');
    $fotoramaDiv.fotorama();
    var fotorama = $fotoramaDiv.data('fotorama');

    // Get reference to the share buttons
    var $fbShareButton = $('#fb-share-button');
    var $twitterShareButton = $('#twitter-share-button');
    var $linkedinShareButton = $('#linkedin-share-button'); // LinkedIn share button

    // Function to update the share URLs
    function updateShareUrls() {
        // Get active frame
        var activeFrame = fotorama.activeFrame;

        // Check if active frame is not null
        if (activeFrame) {
            var currentImage = activeFrame.$stageFrame.find('img').attr('src');

            var fbShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentImage)}`;
            var twitterShareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentImage)}&text=Check%20out%20this%20image!`;
            var linkedinShareUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(currentImage)}&title=Check%20out%20this%20image!`; // LinkedIn share URL

            $fbShareButton.off('click').on('click', function() {
                window.open(fbShareUrl, '_blank');
            });

            $twitterShareButton.off('click').on('click', function() {
                window.open(twitterShareUrl, '_blank');
            });

            $linkedinShareButton.off('click').on('click', function() { // LinkedIn share action
                window.open(linkedinShareUrl, '_blank');
            });
        }
    }

    // Update share URLs when image changes
    $fotoramaDiv.on('fotorama:show', function(e, fotorama) {
        updateShareUrls();
    });

    // Update share URLs immediately on page load
    setTimeout(updateShareUrls, 500);  // Delay added to ensure Fotorama fully loads
});