<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Total Stock KPI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Stock</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalStock) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Total jumlah stok semua item</p>
                            </div>
<div class="self-start sm:self-auto bg-blue-100 rounded-full p-3 sm:p-4">
    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Low Stock Alert KPI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Low Stock Alert</p>
                                <p class="text-3xl font-bold text-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-600 mt-2">{{ $lowStockAlert }}</p>
                                <p class="text-xs text-gray-400 mt-1">Item dengan stok ≤ 10 unit</p>
                            </div>
<div class="self-start sm:self-auto bg-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-100 rounded-full p-3 sm:p-4">
    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-600" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart and QR Scan Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Stock Value per Category Chart --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Value per Category (%)</h3>
<div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
    <div class="w-full max-w-xs sm:max-w-sm md:max-w-[300px]">
        <canvas id="stockValueChart" class="w-full h-full"></canvas>
                            </div>
<div id="chart-legend" class="w-full flex flex-col gap-2 mt-2"></div>
                        </div>
                    </div>
                </div>

{{-- Quick Scan QR --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Scan QR</h3>
<div class="space-y-5">
<div>
    <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">
        Scan atau masukkan QR Code
    </label>
<div class="flex flex-col sm:flex-row gap-3">
<input type="text" id="qr_code" name="qr_code" placeholder="Scan QR code atau ketik manual"
class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
<button type="button" onclick="startCamera()"
class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
<span id="camera-button-text">Camera</span>
</button>
</div>
<button type="button" onclick="scanQR()"
    class="mt-3 w-full px-4 py-2 bg-[#624bff] text-white rounded-md hover:bg-[#5333ff] focus:outline-none focus:ring-2 focus:ring-[#624bff]">
    Cari Item
</button>
</div>

                            {{-- QR Scan Result --}}
                            <div id="qr-result" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
<div class="flex flex-wrap justify-between items-start gap-2 mb-2">
                                    <h4 class="font-semibold text-gray-900">Hasil Scan</h4>
                                    <button type="button" onclick="closeResult()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
<div id="qr-result-content" class="text-sm text-gray-600"></div>
                            </div>

                            {{-- Loading State --}}
                            <div id="qr-loading" class="hidden mt-4 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-sm text-gray-500">Mencari item...</p>
                            </div>

                            {{-- Error State --}}
                            <div id="qr-error" class="hidden mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                <p id="qr-error-message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{-- Sync with Accurate Section --}}
{{--
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Sync with Accurate</h3>
            <button type="button" onclick="getSyncStatus()"
                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Refresh Status
            </button>
        </div>

        <div class="space-y-4"> --}}
            {{-- Sync Status --}}
            {{-- <div id="sync-status" class="hidden p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">Sync Status</h4>
                <div id="sync-status-content" class="text-sm text-gray-600 space-y-1"></div>
            </div> --}}

            {{-- Sync Actions --}}
            {{-- <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="syncWithAccurate()" id="sync-button"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span id="sync-button-text">Sync Inventory</span>
                    </span>
                </button>

                <button type="button" onclick="syncDryRun()"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Dry Run
                </button>
            </div> --}}

            {{-- Sync Result --}}
            {{-- <div id="sync-result" class="hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-green-900">Sync Completed</h4>
                        <p id="sync-result-message" class="text-sm text-green-700 mt-1"></p>
                    </div>
                </div>
            </div> --}}

            {{-- Sync Error --}}
            {{-- <div id="sync-error" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-red-900">Sync Failed</h4>
                        <p id="sync-error-message" class="text-sm text-red-700 mt-1"></p>
                    </div>
                </div>
            </div>

            {{-- Sync Loading --}}
            <div id="sync-loading" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                    <p class="text-sm text-blue-700">Syncing inventory with Accurate...</p>
                </div>
            </div>
        </div>
    </div>
</div> --}}
{{-- End Sync with Accurate Section --}}
        </div>
    </div>

    {{-- Camera Modal for QR Scanning --}}
    <div id="camera-modal" class="hidden fixed inset-0 z-50 bg-black/80 px-4 flex items-center justify-center">
<div class="bg-white rounded-lg p-4 sm:p-6 w-full max-w-sm sm:max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                <h3 class="text-lg font-semibold">Scan QR Code</h3>
                <button type="button" onclick="closeCamera()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
{{-- Camera Selection --}}
<div class="mb-4">
    <label for="modal-camera-select" class="block text-sm font-medium text-gray-700 mb-2">
        Select Camera:
    </label>
    <select id="modal-camera-select" onchange="switchToSelectedCamera()"
        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
        <option value="">Loading cameras...</option>
    </select>
</div>

{{-- Video Element --}}
<div class="relative">
<video id="camera-video" class="w-full rounded-lg aspect-video bg-black" autoplay playsinline></video>
<div id="camera-loading" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-lg">
    <div class="text-white text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
        <p class="text-sm">Starting camera...</p>
    </div>
</div>
</div>

{{-- Camera Controls --}}
<div class="mt-4 flex gap-2">
    <button type="button" onclick="switchCamera()" id="switch-camera-btn"
        class="flex-1 px-4 py-2 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:opacity-50">
        Switch Camera
    </button>
    <button type="button" onclick="toggleFlashlight()" id="flashlight-btn"
        class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 hidden">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
            </path>
        </svg>
    </button>
</div>
            <p class="text-sm text-gray-500 mt-4 text-center">Arahkan kamera ke QR code</p>
<div id="camera-error" class="hidden mt-2 p-2 bg-red-100 border border-red-400 text-red-700 text-sm rounded"></div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
    <script>
        // Chart.js configuration
        const chartData = @json($chartData);

        const ctx = document.getElementById('stockValueChart').getContext('2d');
        const stockValueChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.categories,
                datasets: [{
                    label: 'Stock Percentage (%)',
                    data: chartData.percentages,
                    backgroundColor: [
                        'rgba(98, 75, 255, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    borderColor: [
                        'rgba(98, 75, 255, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
maintainAspectRatio: false,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const percentage = context.parsed || 0;
                                const index = context.dataIndex;
                                const totalValue = chartData.values[index];
                                return `${label}: ${percentage}% (${totalValue.toLocaleString()} units)`;
                            }
                        }
                    }
                }
            }
        });

        // Render custom legend
        function renderCustomLegend() {
            const legendContainer = document.getElementById('chart-legend');
            if (!legendContainer || !chartData.categories) return;

            legendContainer.innerHTML = '';

            chartData.categories.forEach((category, index) => {
                const percentage = chartData.percentages[index];
                const value = chartData.values[index];

                const legendItem = document.createElement('div');
                legendItem.className = 'flex items-center gap-3 p-2 rounded hover:bg-gray-50';
                legendItem.innerHTML = `
                    <div class="w-4 h-4 rounded-full" style="background-color: ${stockValueChart.data.datasets[0].backgroundColor[index]}; border: 2px solid ${stockValueChart.data.datasets[0].borderColor[index]};"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${category}</div>
                        <div class="text-sm text-gray-600">${percentage}% (${value.toLocaleString()} units)</div>
                    </div>
                `;
                legendContainer.appendChild(legendItem);
            });
        }

        // Render legend after chart is created
        renderCustomLegend();

// QR Scanner variables
        let qrScanner = null;
        let stream = null;
let currentCameraId = null;
let availableCameras = [];
let flashlightSupported = false;
let flashlightEnabled = false;

// Initialize camera functionality
async function initializeCamera() {
try {
// Check if camera is supported
if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
throw new Error('Camera API not supported');
}

// Get available cameras
await loadAvailableCameras();
} catch (error) {
console.error('Camera initialization error:', error);
showCameraError('Camera tidak didukung pada perangkat ini');
}
}

// Load available cameras
async function loadAvailableCameras() {
try {
const devices = await navigator.mediaDevices.enumerateDevices();
availableCameras = devices.filter(device => device.kind === 'videoinput');

// Update camera selects
updateCameraSelects();

return availableCameras;
} catch (error) {
console.error('Error loading cameras:', error);
return [];
}
}

// Update camera select dropdowns
function updateCameraSelects() {
const modalSelect = document.getElementById('modal-camera-select');
const inlineSelect = document.getElementById('camera-select');

[modalSelect, inlineSelect].forEach(select => {
if (select) {
select.innerHTML = '<option value="">Pilih kamera...</option>';

availableCameras.forEach((camera, index) => {
const option = document.createElement('option');
option.value = camera.deviceId;

// Create a user-friendly name
let cameraName = camera.label;
if (!cameraName) {
cameraName = `Camera ${index + 1}`;
if (camera.facingMode) {
cameraName += ` (${camera.facingMode === 'environment' ? 'Belakang' : 'Depan'})`;
}
}

option.textContent = cameraName;
select.appendChild(option);
});
}
});
}

// Start camera
async function startCamera() {
            const modal = document.getElementById('camera-modal');
            const video = document.getElementById('camera-video');
const loadingDiv = document.getElementById('camera-loading');
const buttonText = document.getElementById('camera-button-text');

// Prevent multiple camera instances
if (qrScanner && qrScanner.isRunning) {
modal.classList.remove('hidden');
return;
}

try {
// Show modal and loading
modal.classList.remove('hidden');
loadingDiv.classList.remove('hidden');
hideCameraError();

// Check camera permission and availability
if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
throw new Error('Kamera tidak didukung pada perangkat ini');
}

// Request camera permission first
const permissionStatus = await navigator.permissions.query({ name: 'camera' });
if (permissionStatus.state === 'denied') {
throw new Error('Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan browser.');
}

// Initialize cameras if not already done
if (availableCameras.length === 0) {
await loadAvailableCameras();
}

if (availableCameras.length === 0) {
throw new Error('Tidak ada kamera yang tersedia');
}

// Select best camera (prefer back camera for mobile)
if (!currentCameraId) {
const backCamera = availableCameras.find(camera =>
camera.label.toLowerCase().includes('back') ||
camera.label.toLowerCase().includes('belakang') ||
camera.label.toLowerCase().includes('rear') ||
camera.label.toLowerCase().includes('belakang') ||
camera.facingMode === 'environment'
);
currentCameraId = backCamera ? backCamera.deviceId : availableCameras[0].deviceId;
}

// Start camera stream
await startCameraStream(currentCameraId);

// Initialize QR Scanner
QrScanner.hasCamera().then(hasCamera => {
if (hasCamera) {
qrScanner = new QrScanner(
video,
result => {
// Provide haptic feedback on mobile
if (navigator.vibrate) {
navigator.vibrate(200);
}
document.getElementById('qr_code').value = result.data;
closeCamera();
scanQR();
                                },
                                {
returnDetailedScanResult: true,
highlightScanRegion: true,
highlightCodeOutline: true
                                }
);
qrScanner.start().then(() => {
loadingDiv.classList.add('hidden');
buttonText.textContent = 'Camera Active';
}).catch(error => {
console.error('QR Scanner start error:', error);
loadingDiv.classList.add('hidden');
showCameraError('Gagal memulai pemindai QR: ' + error.message);
});
} else {
loadingDiv.classList.add('hidden');
showCameraError('Kamera tidak tersedia untuk pemindaian QR');
}
});
} catch (error) {
console.error('Error starting camera:', error);
loadingDiv.classList.add('hidden');
modal.classList.add('hidden');

// Provide specific error messages
let errorMessage = error.message;
if (error.name === 'NotAllowedError') {
errorMessage = 'Izin kamera ditolak. Silakan izinkan akses kamera dan coba lagi.';
} else if (error.name === 'NotFoundError') {
errorMessage = 'Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
} else if (error.name === 'NotReadableError') {
errorMessage = 'Kamera sedang digunakan oleh aplikasi lain.';
}

alert(errorMessage);
}
}

// Start camera stream with specific device
async function startCameraStream(deviceId = null) {
const video = document.getElementById('camera-video');

// Stop existing stream
if (stream) {
stream.getTracks().forEach(track => track.stop());
}

const constraints = {
video: {
deviceId: deviceId ? { exact: deviceId } : undefined,
facingMode: deviceId ? undefined : 'environment',
width: { ideal: 1280 },
height: { ideal: 720 }
}
};

try {
stream = await navigator.mediaDevices.getUserMedia(constraints);
video.srcObject = stream;

// Check for flashlight support
checkFlashlightSupport();

return stream;
} catch (error) {
// Fallback: try without deviceId constraint
if (deviceId && error.name === 'OverconstrainedError') {
console.warn('Device constraint failed, trying without deviceId');
return startCameraStream(null);
}
throw error;
}
}

// Switch to selected camera
async function switchToSelectedCamera() {
const select = document.getElementById('modal-camera-select');
const selectedDeviceId = select.value;

if (selectedDeviceId && selectedDeviceId !== currentCameraId) {
try {
currentCameraId = selectedDeviceId;
await startCameraStream(selectedDeviceId);

// Update inline select if it exists
const inlineSelect = document.getElementById('camera-select');
if (inlineSelect) {
inlineSelect.value = selectedDeviceId;
}
} catch (error) {
console.error('Error switching camera:', error);
showCameraError('Gagal mengganti kamera');
}
}
}

// Switch camera (toggle between available cameras)
async function switchCamera() {
if (availableCameras.length < 2) { showCameraError('Hanya satu kamera tersedia'); return; } try { const
    currentIndex=availableCameras.findIndex(camera=> camera.deviceId === currentCameraId);
    const nextIndex = (currentIndex + 1) % availableCameras.length;
    const nextCamera = availableCameras[nextIndex];

    await switchToSelectedCamera();
    document.getElementById('modal-camera-select').value = nextCamera.deviceId;

    } catch (error) {
    console.error('Error switching camera:', error);
    showCameraError('Gagal mengganti kamera');
    }
    }

    // Check flashlight support
    async function checkFlashlightSupport() {
    if (!stream) return;

    try {
    const videoTrack = stream.getVideoTracks()[0];
    const capabilities = videoTrack.getCapabilities();

    flashlightSupported = capabilities.torch || false;

    const flashlightBtn = document.getElementById('flashlight-btn');
    if (flashlightBtn) {
    flashlightBtn.classList.toggle('hidden', !flashlightSupported);
    }
    } catch (error) {
    console.error('Error checking flashlight support:', error);
    flashlightSupported = false;
    }
    }

    // Toggle flashlight
    async function toggleFlashlight() {
    if (!flashlightSupported || !stream) return;

    try {
    const videoTrack = stream.getVideoTracks()[0];
    await videoTrack.applyConstraints({
    advanced: [{ torch: !flashlightEnabled }]
                });
flashlightEnabled = !flashlightEnabled;

const flashlightBtn = document.getElementById('flashlight-btn');
if (flashlightBtn) {
flashlightBtn.classList.toggle('bg-yellow-600', !flashlightEnabled);
flashlightBtn.classList.toggle('bg-yellow-800', flashlightEnabled);
}
} catch (error) {
console.error('Error toggling flashlight:', error);
showCameraError('Gagal mengaktifkan flash');
}
        }

// Close camera
        function closeCamera() {
            const modal = document.getElementById('camera-modal');
const cameraControls = document.getElementById('camera-controls');
const buttonText = document.getElementById('camera-button-text');

// Stop QR scanner
            if (qrScanner) {
                qrScanner.stop();
                qrScanner.destroy();
                qrScanner = null;
            }

// Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

// Reset flashlight
flashlightEnabled = false;

// Update UI
modal.classList.add('hidden');
cameraControls.classList.add('hidden');
buttonText.textContent = 'Camera';
hideCameraError();
}

// Show camera error
function showCameraError(message) {
const errorDiv = document.getElementById('camera-error');
if (errorDiv) {
errorDiv.textContent = message;
errorDiv.classList.remove('hidden');
} else {
alert(message);
}
}

// Hide camera error
function hideCameraError() {
const errorDiv = document.getElementById('camera-error');
if (errorDiv) {
errorDiv.classList.add('hidden');
}
        }

        function scanQR() {
            const qrCode = document.getElementById('qr_code').value.trim();

            if (!qrCode) {
                alert('Masukkan QR code terlebih dahulu');
                return;
            }

            const resultDiv = document.getElementById('qr-result');
            const loadingDiv = document.getElementById('qr-loading');
            const errorDiv = document.getElementById('qr-error');
            const resultContent = document.getElementById('qr-result-content');

            // Hide previous results
            resultDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');

            fetch('{{ route("admin.inventory.dashboard.scan-qr") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.add('hidden');

                if (data.success) {
                    const item = data.data;
                    resultContent.innerHTML = `
                        <div class="space-y-2">
                            <div><span class="font-medium">Tipe:</span> ${item.type}</div>
                            <div><span class="font-medium">Kode Item:</span> ${item.item_code}</div>
                            <div><span class="font-medium">Nama Item:</span> ${item.item_name}</div>
                            <div><span class="font-medium">Quantity:</span> ${item.quantity}</div>
                            ${item.batch_no ? `<div><span class="font-medium">Batch:</span> ${item.batch_no}</div>` : ''}
                            <div class="pt-2 border-t">
                                <div><span class="font-medium">Lokasi:</span> ${item.location}</div>
                                <div><span class="font-medium">Rak:</span> ${item.rack}</div>
                                <div><span class="font-medium">Pallet:</span> ${item.pallet}</div>
                            </div>
                            <div class="pt-2"><span class="font-medium">Status:</span> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">${item.status}</span></div>
                        </div>
                    `;
                    resultDiv.classList.remove('hidden');
                } else {
                    document.getElementById('qr-error-message').textContent = data.message || 'Item tidak ditemukan';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                document.getElementById('qr-error-message').textContent = 'Terjadi kesalahan saat mencari item';
                errorDiv.classList.remove('hidden');
                console.error('Error:', error);
            });
        }

        function closeResult() {
            document.getElementById('qr-result').classList.add('hidden');
            document.getElementById('qr_code').value = '';
        }

// Sync Functions
function syncWithAccurate() {
performSync(false);
}

function syncDryRun() {
performSync(true);
}

function performSync(isDryRun = false) {
const button = document.getElementById('sync-button');
const buttonText = document.getElementById('sync-button-text');
const loadingDiv = document.getElementById('sync-loading');
const resultDiv = document.getElementById('sync-result');
const errorDiv = document.getElementById('sync-error');

// Reset states
loadingDiv.classList.remove('hidden');
resultDiv.classList.add('hidden');
errorDiv.classList.add('hidden');
button.disabled = true;
buttonText.textContent = isDryRun ? 'Running Dry Run...' : 'Syncing...';

fetch('{{ route("admin.inventory.sync.accurate") }}', {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'X-CSRF-TOKEN': '{{ csrf_token() }}'
},
body: JSON.stringify({ dry_run: isDryRun })
})
.then(response => response.json())
.then(data => {
loadingDiv.classList.add('hidden');
button.disabled = false;
buttonText.textContent = 'Sync Inventory';

if (data.success) {
document.getElementById('sync-result-message').textContent = data.message;
resultDiv.classList.remove('hidden');
// Refresh sync status after successful sync
getSyncStatus();
} else {
document.getElementById('sync-error-message').textContent = data.message || 'Sync failed';
errorDiv.classList.remove('hidden');
}
})
.catch(error => {
loadingDiv.classList.add('hidden');
button.disabled = false;
buttonText.textContent = 'Sync Inventory';
document.getElementById('sync-error-message').textContent = 'An error occurred during sync';
errorDiv.classList.remove('hidden');
console.error('Sync error:', error);
});
}

function getSyncStatus() {
const statusDiv = document.getElementById('sync-status');
const statusContent = document.getElementById('sync-status-content');

fetch('{{ route("admin.inventory.sync.status") }}', {
method: 'GET',
headers: {
'X-CSRF-TOKEN': '{{ csrf_token() }}'
}
})
.then(response => response.json())
.then(data => {
if (data.success) {
const stats = data.data;
statusContent.innerHTML = `
<div><strong>Total Items:</strong> ${stats.total_inventory_items}</div>
<div><strong>Last Synced:</strong> ${stats.last_synced_count}</div>
<div><strong>Need Sync:</strong> ${stats.needing_sync_count}</div>
${stats.last_synced_items && stats.last_synced_items.length > 0 ?
`<div class="mt-2"><strong>Recent Syncs:</strong></div>` +
stats.last_synced_items.map(item =>
`<div class="ml-2 text-xs">• ${item.item_code} - ${item.item_name} (${item.last_synced_at})</div>`
).join('') : ''
}
`;
statusDiv.classList.remove('hidden');
}
})
.catch(error => {
console.error('Status error:', error);
});
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
initializeCamera();
getSyncStatus();
});
        // Allow Enter key to trigger scan
        document.getElementById('qr_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                scanQR();
            }
        });
    </script>
    @endpush
</x-app-layout>

