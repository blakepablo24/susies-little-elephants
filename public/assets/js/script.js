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
      modal.style.display = "grid";
      modalImg.src = this.src;
    }
}

var span = document.getElementsByClassName("enlarge-image-modal-close-btn")[0];
span.onclick = function() { 
  modal.style.display = "none";
}

function openTab(evt, tabName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("tab");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" selected", "");
    }
    document.getElementById(tabName).style.display = "grid";
    evt.currentTarget.className += " selected";
  }