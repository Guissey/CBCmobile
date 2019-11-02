$(document).ready(function(){
	$('.dropdown-submenu a.test').on("click", function(e){
    		$(this).next('ul').toggle();
    		e.stopPropagation();
		e.preventDefault();
 	 });
	if (window.File && window.FileReader && window.FormData) {
	var $inputField = $('#file');

	$inputField.on('change', function (e) {
		var files = e.target.files;

		if (files) {
			for (var i = 0; i < files.length; i++) {
				if (/^image\//i.test(files[i].type)) {
					readFile(files[i]);
				} else {
				alert('Image non valide!');
				}
			}
		}
	});
} else {
	alert("Votre navigateur ne permet pas de partager vos photos!");
};

document.getElementById("inf-scroll-container").addEventListener("scroll", infiniteScroll);
});

function showCarousel(preview){
	var id = preview.id;
	var number = id.replace('preview', '');
	number = parseInt(number,10);
	$('.carousel').carousel(number);
	document.getElementById("carousel").style.display = "block";
}

function closeCarousel(){
	document.getElementById("carousel").style.display = "none";
}

function rotateSlide(){
	var currentIndex = $("div.active").index();
	var divID = "slide" + currentIndex;
	var slide = document.getElementById(divID);
	if (slide.classList.contains("item-straight")){
		slide.className = "item-rotated";
		slide.parentNode.className = "item-rotated-parent";
	}
	else{
		slide.className = "item-straight";
		slide.parentNode.className = "item-straight-parent";
	}
}

function isUploadSupported() {
    if (navigator.userAgent.match(/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) {
        return false;
    }
    var elem = document.createElement('input');
    elem.type = 'file';
    return !elem.disabled;
};



function readFile(file) {
	var reader = new FileReader();

	reader.onloadend = function () {
		processFile(reader.result, file.type);
	}

	reader.onerror = function () {
		alert('Le navigateur n\'arrive pas à lire la photo');
	}

	reader.readAsDataURL(file);
}


function processFile(dataURL, fileType) {
	var maxWidth = 800;
	var maxHeight = 800;

	var image = new Image();
	image.src = dataURL;

	image.onload = function () {
		var width = image.width;
		var height = image.height;
		var shouldResize = (width > maxWidth) || (height > maxHeight);

		if (!shouldResize) {
			sendFile(dataURL);
			return;
		}

		var newWidth;
		var newHeight;

		if (width > height) {
			newHeight = height * (maxWidth / width);
			newWidth = maxWidth;
		} else {
			newWidth = width * (maxHeight / height);
			newHeight = maxHeight;
		}

		var canvas = document.createElement('canvas');

		canvas.width = newWidth;
		canvas.height = newHeight;

		var context = canvas.getContext('2d');

		context.drawImage(this, 0, 0, newWidth, newHeight);

		dataURL = canvas.toDataURL(fileType);

		sendFile(dataURL);
	};

	image.onerror = function () {
		alert('Erreur lors du traitement de la photo');
	};
}


function sendFile(fileData) {
	var formData = new FormData();
	var urlParams = new URLSearchParams(window.location.search);
	var tournament_id = urlParams.get('id');

	formData.append('imageData', fileData);
	formData.append('id', tournament_id);

	$.ajax({
		type: 'POST',
		url: 'ajaxcall.php',
		data: formData,
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (data) {
			if (data.success) {
				alert('Photo sauvegardée! Rechargez la page pour la voir');
				console.log(data.image);
				console.log(data.path);
				//document.write('<img src="' + data.path + '" />');
			} else {
				alert('Erreur lors de la sauvegarde de la photo sur le serveur');
			}
		},
		error: function (data) {
			alert('Erreur lors de l\'envoi de la photo');
		}
	});
}


function deleteImage() {
	var currentIndex = $('div.active').index();
	var preview = document.getElementById("preview" + currentIndex);
	var imagePath = preview.src;
	imagePath = imagePath.split('cbc/')[1];
	//alert(imagePath);
	$.ajax({
		type: "POST",
		url: "deletepicture.php",
		data: {path: imagePath}, 
		dataType: 'json',
		success: function(data){
			if (data.success) {
				deleteAsync();
			}
			else{
				alert("Photo non trouvée sur le serveur");
			}
		},
		error: function (data) {
			alert('Erreur lors de la supression de la photo');
		}
	});
}

function deleteAsync(){
	var carousel = $('#carousel');
	var currentIndex = $('div.active').index();
	var activeItem = document.getElementById("item" + currentIndex);
	var preview = document.getElementById("preview" + currentIndex);
  	carousel.carousel('next');
  	activeItem.remove();
	preview.remove();
	var totalItems = $('.item').length;
	for (var i=currentIndex+1; i<=totalItems; i++){
		var j=i-1;
		var item = document.getElementById("item" + i);
		item.id = "item" + j;
		var slide = document.getElementById("slide" + i);
		slide.id = "slide" + j;
		var preview = document.getElementById("preview" + i);
		preview.id = "preview" + j;
	}
}

var offset = 10;
var ajaxready = true

function infiniteScroll(){
	if (ajaxready == false) {return};
	var scrolltop = this.scrollTop;
	var scrollHeight = this.scrollHeight;
	var clientHeight = this.clientHeight;
	var scrollBot = scrollHeight - clientHeight - scrolltop;
	//alert("scroll:" + scrollHeight + " client: " + clientHeight + " scrollTop " + scrolltop + " scrollBot " + scrollBot);
	if (scrollBot < clientHeight/10){
		ajaxready = false;
		$('#inf-scroll-container #loader').fadeIn(400);

		$.ajax({
		type: "GET",
		url: "ajaxcall2.php",
		data: {"offset": offset},
		success: function(data){
			if (data != "") {
				console.log(data);
				$('#inf-scroll-container #loader').before(data);
				offset += 10;
				ajaxready = true;
			}	
			else{
				document.getElementById("inf-scroll-container").removeEventListener("scroll", infiniteScroll);
				console.log("No more");
			}
			},
		error: function (data) {
			alert('Erreur lors de la demande pour afficher pllus de tournois');
		}
		});
		$('#inf-scroll-container #loader').fadeOut(400);
	}
}
