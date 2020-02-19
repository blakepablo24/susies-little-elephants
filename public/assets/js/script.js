function openBurgerMenu() {
    document.getElementById("burger-menu").style.height = "100%";
}

function closeBurgerMenu() {
    document.getElementById("burger-menu").style.height = "0%";
}

function revealPostForm() {
    document.getElementById("new-time-line-post-form").style.display = "block";
    document.getElementById("add-new-time-line-post-form-button").style.display = "none";
    document.getElementById("hide-new-time-line-post-form-button").style.display = "block";
}

function hidePostForm() {
    document.getElementById("new-time-line-post-form").style.display = "none";
    document.getElementById("add-new-time-line-post-form-button").style.display = "block";
    document.getElementById("hide-new-time-line-post-form-button").style.display = "none";
}

// Timeline post enlarge image modal

var modal = document.getElementById("enlargeImageModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
var img = document.getElementById("timeline-post-mini-image");
var images = document.getElementsByClassName('allPostImages');
var modalImg = document.getElementById("img01");

for (var i = 0; i < images.length; i++) {
    var img = images[i];
    // and attach our click listener for this image.
    img.onclick = function(evt) {
      modal.style.display = "flex";
      modalImg.src = this.src;
    }
}

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("enlarge-image-modal-close-btn")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
  modal.style.display = "none";
}