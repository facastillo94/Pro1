/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
function getParameterByName(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
	results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
//alert('DATO: ' + getParameterByName('iduser'));
document.getElementById('iduser').value = getParameterByName('iduser');
if(getParameterByName('nombremodelo') != ''){
	document.getElementById('nombremodelo').value = getParameterByName('nombremodelo');
}
if(getParameterByName('ncedula') != ''){
	document.getElementById('ncedula').value = getParameterByName('ncedula');
}
if(getParameterByName('imgfrontal') != ''){
	document.getElementById('imgfrontal').value = getParameterByName('imgfrontal');
}
if(getParameterByName('imgfrontal2') != ''){
	document.getElementById('imgfrontal2').value = getParameterByName('imgfrontal2');
}
if(getParameterByName('imgfrontal3') != ''){
	document.getElementById('imgfrontal3').value = getParameterByName('imgfrontal3');
}
var app = {
    // Application Constructor
    initialize: function() {
        document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
    },

    // deviceready Event Handler
    //
    // Bind any cordova events here. Common events are:
    // 'pause', 'resume', etc.
    onDeviceReady: function() {
		// Method below REQUIRES elements we removed from body in index.html
		// So we comment it out.
		// this.receivedEvent('deviceready');
		screen.orientation.lock('portrait');
		let options = {
			x: 0,
			y: 0,
			width: window.screen.width,
			height: window.screen.height,
			camera: CameraPreview.CAMERA_DIRECTION.BACK,  // Front/back camera
			toBack: true,   // Set to true if you want your html in front of your preview
			tapPhoto: false,  // Tap to take photo
			tapFocus: true,   // Tap to focus
			previewDrag: false
		};

		var flash_mode = 'on';
		// Take a look at docs: https://github.com/cordova-plugin-camera-preview/cordova-plugin-camera-preview#methods
		CameraPreview.startCamera(options);


		// Create a rectangle & buttons
		var rect = document.createElement('div');
		var take_pic_btn = document.createElement('img');
		var flash_on_btn = document.createElement('img');
		var flash_off_btn = document.createElement('img');

		// You must specify path relative to www folder
		take_pic_btn.src = 'img/btn_icon_mini.png';
		flash_on_btn.src = 'img/switch_camera.png';
		flash_off_btn.src = 'img/flash_off.svg';

		// Add styles
		rect.className += 'rect_class';
		take_pic_btn.className += 'btn_class';
		flash_on_btn.className += 'btn_class_flash';
		flash_off_btn.className += 'btn_class';

		take_pic_btn.className += ' take_pic_class'
		flash_on_btn.className += ' flash_class'
		flash_off_btn.className += ' flash_class'

		// Hide flash_off btn by default
		flash_off_btn.style.visibility = 'hidden';

		// Append to body section
		document.body.appendChild(rect);
		document.body.appendChild(take_pic_btn);
		document.body.appendChild(flash_on_btn);
		var rect_coords = rect.getBoundingClientRect();
		var x_coord = rect_coords.left, y_coord = rect_coords.top;
		take_pic_btn.onclick = function(){
			// Get rectangle size
			var rect_width = rect.offsetWidth, rect_height = rect.offsetHeight;

			CameraPreview.takePicture(function(base64PictureData) {

				// We pass width, height, x and y coordinates of our rectangle to crop method
				// At the very end, crop methods send cropped image to server
				
				var cropped_img = crop(base64PictureData, rect_width, rect_height, x_coord, y_coord, function(cropped_img_base64) {
					if(confirm("Deseas Enviar La Fotografia?") == true){
						window.localStorage.setItem("imgfrontal", cropped_img_base64);
					}
					/*document.getElementById('imgfrontal').value = cropped_img_base64;*/
					miForm.method="GET";
					miForm.action="index.html";
					miForm.submit();
				});
			});
		};

		flash_on_btn.onclick = function() {
			/*flash_mode = 'on';
			flash_off_btn.style.visibility = 'visible';
			flash_on_btn.style.visibility = 'hidden';

			CameraPreview.setFlashMode(flash_mode);*/
			CameraPreview.switchCamera();
		}

		flash_off_btn.onclick = function() {
			flash_mode = 'off';
			flash_off_btn.style.visibility = 'hidden';
			flash_on_btn.style.visibility = 'visible';

			CameraPreview.setFlashMode(flash_mode);
		}

		
	},

    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
        var receivedElement = parentElement.querySelector('.received');

        listeningElement.setAttribute('style', 'display:none;');
        receivedElement.setAttribute('style', 'display:block;');

        console.log('Received Event: ' + id);
    }
};

app.initialize();