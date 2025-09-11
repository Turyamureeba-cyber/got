<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ugandan ID Card Information Extractor</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7f9 0%, #e4e8ec 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        
        h1 {
            color: #1a73e8;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        
        .subtitle {
            color: #5f6368;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .content {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 20px;
        }
        
        .upload-section {
            flex: 1;
            min-width: 300px;
        }
        
        .results-section {
            flex: 1;
            min-width: 300px;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #1a73e8;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .image-options {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: #1a73e8;
            color: white;
            box-shadow: 0 4px 6px rgba(26, 115, 232, 0.2);
        }
        
        .btn-primary:hover {
            background: #0d62c9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f1f3f4;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #dfe1e5;
            transform: translateY(-2px);
        }
        
        .preview-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            border: 2px dashed #ccc;
            display: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .camera-container {
            margin-top: 20px;
            display: none;
        }
        
        #cameraStream {
            width: 100%;
            border-radius: 10px;
            background: #eee;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .camera-controls {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .result-item {
            margin-bottom: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            border-left: 4px solid #1a73e8;
        }
        
        .result-label {
            font-weight: 700;
            color: #5f6368;
            display: block;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        .result-value {
            font-size: 1.3rem;
            color: #202124;
            word-break: break-all;
            font-weight: 600;
        }
        
        .id-sample {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
        }
        
        .id-sample img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }
        
        .note {
            background: #fff8e1;
            padding: 20px;
            border-left: 4px solid #ffc107;
            margin: 25px 0;
            border-radius: 8px;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .highlight {
            background: #e3f2fd;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: 600;
            color: #1a73e8;
        }
        
        .progress-bar {
            height: 8px;
            background: #f1f3f4;
            border-radius: 4px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(90deg, #1a73e8 0%, #0d62c9 100%);
            width: 0%;
            transition: width 0.4s ease;
            border-radius: 4px;
        }
        
        .status {
            text-align: center;
            margin: 15px 0;
            font-weight: 600;
            color: #5f6368;
        }
        
        .success {
            color: #0f9d58;
        }
        
        .error {
            color: #db4437;
        }
        
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .container {
                padding: 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Ugandan ID Card Information Extractor</h1>
            <p class="subtitle">Extract ID numbers and names from Ugandan National ID cards using advanced OCR technology</p>
        </header>
        
        <div class="content">
            <div class="upload-section">
                <h2 class="section-title">Upload ID Image</h2>
                
                <div class="card">
                    <div class="image-options">
                        <button id="uploadFileBtn" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Image
                        </button>
                        <button id="takePhotoBtn" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                            Take Photo
                        </button>
                    </div>
                    
                    <input type="file" id="fileInput" accept="image/*" style="display: none;">
                    
                    <div class="preview-container">
                        <img id="imagePreview" class="image-preview" alt="Image preview">
                    </div>
                    
                    <div class="camera-container">
                        <video id="cameraStream" autoplay playsinline></video>
                        <div class="camera-controls">
                            <button id="captureBtn" class="btn btn-primary">Capture</button>
                            <button id="switchCameraBtn" class="btn btn-secondary">Switch Camera</button>
                        </div>
                        <canvas id="canvas" style="display: none;"></canvas>
                    </div>
                    
                    <div class="note">
                        <p><strong>Note:</strong> For best results, ensure the ID card is well-lit and all text is clearly visible. Position the ID card so text is straight and not skewed.</p>
                    </div>
                    
                    <button id="processBtn" class="btn btn-primary" style="width: 100%; margin-top: 20px; display: none; padding: 16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        Extract Information
                    </button>
                    
                    <div class="progress-bar" id="progressContainer" style="display: none;">
                        <div class="progress" id="progressBar"></div>
                    </div>
                    
                    <div class="status" id="statusMessage"></div>
                </div>
                
                <div class="id-sample">
                    <h3>Expected Ugandan ID Card Format</h3>
                    <div style="background: #f8f8f8; padding: 20px; border-radius: 10px; text-align: left; font-family: monospace; line-height: 1.8; font-size: 1.1rem; margin-top: 15px;">
                        <div>REPUBLIC OF UGANDA</div>
                        <div>NATIONAL ID CARD</div>
                        <div>SPECIMEN</div>
                        <div style="margin-top: 15px; font-weight: bold; color: #1a73e8;">MARTIN</div>
                        <div style="font-weight: bold; color: #1a73e8;">UTOPIAN M</div>
                        <div style="margin-top: 15px;">28.09.1964</div>
                        <div style="font-weight: bold; color: #0f9d58;">1234567891234</div>
                        <div>0123456789</div>
                        <div>01.04.2018</div>
                        <div style="margin-top: 15px; color: #db4437;">Martin Speziano</div>
                    </div>
                    <p style="margin-top: 15px;">The system will extract the <span class="highlight">ID number</span> and <span class="highlight">name</span> from similar ID cards.</p>
                </div>
            </div>
            
            <div class="results-section">
                <h2 class="section-title">Extracted Information</h2>
                
                <div class="card">
                    <div class="result-item">
                        <span class="result-label">ID Number</span>
                        <span id="idNumber" class="result-value">-</span>
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Full Name</span>
                        <span id="fullName" class="result-value">-</span>
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Date of Birth</span>
                        <span id="dob" class="result-value">-</span>
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Issue Date</span>
                        <span id="issueDate" class="result-value">-</span>
                    </div>
                    
                    <div class="result-item">
                        <span class="result-label">Raw OCR Text</span>
                        <textarea id="rawText" style="width: 100%; height: 120px; padding: 15px; margin-top: 10px; border-radius: 8px; border: 1px solid #ddd; font-family: monospace; font-size: 0.9rem;" readonly></textarea>
                    </div>
                </div>
                
                <div class="note">
                    <p><strong>How it works:</strong> The system uses Tesseract.js OCR to extract text from the ID card image, then applies advanced pattern matching to identify the ID number and name based on the structure of Ugandan ID cards.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Tesseract.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.0.3/dist/tesseract.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const uploadFileBtn = document.getElementById('uploadFileBtn');
            const takePhotoBtn = document.getElementById('takePhotoBtn');
            const fileInput = document.getElementById('fileInput');
            const imagePreview = document.getElementById('imagePreview');
            const cameraContainer = document.querySelector('.camera-container');
            const cameraStream = document.getElementById('cameraStream');
            const captureBtn = document.getElementById('captureBtn');
            const switchCameraBtn = document.getElementById('switchCameraBtn');
            const processBtn = document.getElementById('processBtn');
            const canvas = document.getElementById('canvas');
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const statusMessage = document.getElementById('statusMessage');
            const ctx = canvas.getContext('2d');
            
            // Result elements
            const idNumberEl = document.getElementById('idNumber');
            const fullNameEl = document.getElementById('fullName');
            const dobEl = document.getElementById('dob');
            const issueDateEl = document.getElementById('issueDate');
            const rawTextEl = document.getElementById('rawText');
            
            let currentStream = null;
            let facingMode = 'environment'; // Start with back camera
            
            // Upload file button click handler
            uploadFileBtn.addEventListener('click', () => {
                fileInput.click();
            });
            
            // File input change handler
            fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        cameraContainer.style.display = 'none';
                        processBtn.style.display = 'block';
                        statusMessage.textContent = 'Image ready for processing';
                        statusMessage.className = 'status success';
                        
                        // Stop camera if active
                        if (currentStream) {
                            stopCamera();
                        }
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Take photo button click handler
            takePhotoBtn.addEventListener('click', () => {
                startCamera();
                imagePreview.style.display = 'none';
                processBtn.style.display = 'block';
            });
            
            // Capture button click handler
            captureBtn.addEventListener('click', () => {
                canvas.width = cameraStream.videoWidth;
                canvas.height = cameraStream.videoHeight;
                ctx.drawImage(cameraStream, 0, 0, canvas.width, canvas.height);
                
                // Convert canvas to image and display
                imagePreview.src = canvas.toDataURL('image/png');
                imagePreview.style.display = 'block';
                cameraContainer.style.display = 'none';
                statusMessage.textContent = 'Image captured. Ready to process.';
                statusMessage.className = 'status success';
                
                // Stop camera
                stopCamera();
            });
            
            // Switch camera button click handler
            switchCameraBtn.addEventListener('click', () => {
                // Toggle between front and back camera
                facingMode = facingMode === 'environment' ? 'user' : 'environment';
                stopCamera();
                startCamera();
            });
            
            // Process button click handler
            processBtn.addEventListener('click', () => {
                processImage(imagePreview.src);
            });
            
            // Start camera function
            function startCamera() {
                cameraContainer.style.display = 'block';
                statusMessage.textContent = 'Starting camera...';
                statusMessage.className = 'status';
                
                const constraints = {
                    video: { facingMode: facingMode },
                    audio: false
                };
                
                navigator.mediaDevices.getUserMedia(constraints)
                    .then(stream => {
                        cameraStream.srcObject = stream;
                        currentStream = stream;
                        statusMessage.textContent = 'Camera ready. Click capture to take a photo.';
                        statusMessage.className = 'status success';
                    })
                    .catch(error => {
                        console.error('Error accessing camera:', error);
                        statusMessage.textContent = 'Unable to access camera: ' + error.message;
                        statusMessage.className = 'status error';
                    });
            }
            
            // Stop camera function
            function stopCamera() {
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
            }
            
            // Process image with OCR
            function processImage(imageSrc) {
                // Show loading state
                idNumberEl.textContent = 'Processing...';
                fullNameEl.textContent = 'Processing...';
                dobEl.textContent = 'Processing...';
                issueDateEl.textContent = 'Processing...';
                rawTextEl.textContent = 'Extracting text...';
                statusMessage.textContent = 'Processing image with OCR...';
                statusMessage.className = 'status';
                
                // Show progress bar
                progressContainer.style.display = 'block';
                progressBar.style.width = '0%';
                
                Tesseract.recognize(
                    imageSrc,
                    'eng',
                    { 
                        logger: message => {
                            console.log(message);
                            if (message.status === 'recognizing text') {
                                progressBar.style.width = `${message.progress * 100}%`;
                                statusMessage.textContent = `Processing: ${Math.round(message.progress * 100)}%`;
                            }
                        }
                    }
                ).then(({ data: { text } }) => {
                    // Display raw OCR text
                    rawTextEl.textContent = text;
                    
                    // Parse the extracted text
                    const parsedData = parseIDText(text);
                    
                    // Update the UI with parsed data
                    idNumberEl.textContent = parsedData.idNumber || 'Not found';
                    fullNameEl.textContent = parsedData.fullName || 'Not found';
                    dobEl.textContent = parsedData.dob || 'Not found';
                    issueDateEl.textContent = parsedData.issueDate || 'Not found';
                    
                    // Hide progress bar
                    progressContainer.style.display = 'none';
                    
                    if (parsedData.idNumber && parsedData.fullName) {
                        statusMessage.textContent = 'Information extracted successfully!';
                        statusMessage.className = 'status success';
                    } else {
                        statusMessage.textContent = 'Some information could not be extracted. Please check the raw OCR text.';
                        statusMessage.className = 'status error';
                    }
                }).catch(error => {
                    console.error('OCR Error:', error);
                    statusMessage.textContent = 'Error processing image: ' + error.message;
                    statusMessage.className = 'status error';
                    progressContainer.style.display = 'none';
                });
            }
            
            // Parse Ugandan ID text with improved pattern matching
            function parseIDText(text) {
                const lines = text.split('\n').map(line => line.trim()).filter(line => line.length > 0);
                
                let idNumber = '';
                let fullName = '';
                let dob = '';
                let issueDate = '';
                
                console.log("OCR Output Lines:", lines);
                
                // Look for ID number (13 or 14 digits)
                const idPattern = /\b\d{13,14}\b/;
                
                // Look for date patterns (DD.MM.YYYY)
                const datePattern = /\b\d{2}\.\d{2}\.\d{4}\b/g;
                
                // Look for name (typically appears after "REPUBLIC OF UGANDA" and before the dates)
                let nameFound = false;
                let nameLines = [];
                
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i];
                    
                    // Check for ID number
                    if (!idNumber && idPattern.test(line)) {
                        idNumber = line.match(idPattern)[0];
                        continue;
                    }
                    
                    // Check for dates
                    if (datePattern.test(line)) {
                        const dates = line.match(datePattern);
                        if (dates) {
                            // First date is likely DOB
                            if (!dob) dob = dates[0];
                            
                            // Check if there's a second date (issue date)
                            if (dates.length > 1 && !issueDate) {
                                issueDate = dates[1];
                            } else if (i + 1 < lines.length) {
                                // Check next line for date
                                const nextLineDates = lines[i + 1].match(datePattern);
                                if (nextLineDates && nextLineDates.length > 0 && !issueDate) {
                                    issueDate = nextLineDates[0];
                                }
                            }
                        }
                        continue;
                    }
                    
                    // Check for name - typically appears after "REPUBLIC OF UGANDA" or "NATIONAL ID CARD"
                    if (!nameFound && (
                        line.toUpperCase().includes('REPUBLIC OF UGANDA') || 
                        line.toUpperCase().includes('NATIONAL ID CARD') ||
                        line.toUpperCase().includes('SPECIMEN')
                    )) {
                        // Name is likely 1-3 lines after this
                        for (let j = i + 1; j < Math.min(i + 4, lines.length); j++) {
                            if (lines[j] && lines[j].length > 3 && 
                                !lines[j].toUpperCase().includes('NATIONAL') && 
                                !lines[j].toUpperCase().includes('ID') && 
                                !lines[j].toUpperCase().includes('CARD') &&
                                !lines[j].toUpperCase().includes('REPUBLIC') &&
                                !lines[j].toUpperCase().includes('UGANDA') &&
                                !lines[j].toUpperCase().includes('SPECIMEN') &&
                                !/\b\d{2}\.\d{2}\.\d{4}\b/.test(lines[j]) &&
                                !/\b\d{13,14}\b/.test(lines[j])) {
                                
                                nameLines.push(lines[j]);
                            }
                        }
                        
                        if (nameLines.length > 0) {
                            fullName = nameLines.join(' ');
                            nameFound = true;
                        }
                    }
                }
                
                // If we haven't found dates with the pattern, try to find any date-like strings
                if (!dob || !issueDate) {
                    const allText = text.replace(/\n/g, ' ');
                    const allDates = allText.match(/\b\d{2}\.\d{2}\.\d{4}\b/g) || [];
                    
                    if (allDates.length >= 2) {
                        if (!dob) dob = allDates[0];
                        if (!issueDate) issueDate = allDates[1];
                    } else if (allDates.length === 1 && !dob) {
                        dob = allDates[0];
                    }
                }
                
                // If we haven't found the ID number with the pattern, try to find any long digit sequences
                if (!idNumber) {
                    const digitSequence = text.match(/\b\d{10,}\b/);
                    if (digitSequence) {
                        idNumber = digitSequence[0];
                    }
                }
                
                // If we haven't found the name with the pattern, try to find text that looks like a name
                if (!fullName) {
                    for (let i = 0; i < lines.length; i++) {
                        const line = lines[i];
                        if (line.length > 5 && /^[A-Z][a-z]+([\s-][A-Z][a-z]+)*$/.test(line)) {
                            fullName = line;
                            break;
                        }
                    }
                }
                
                return {
                    idNumber,
                    fullName,
                    dob,
                    issueDate
                };
            }
        });
    </script>
</body>
</html>