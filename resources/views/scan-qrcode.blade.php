<x-app-layout>
    <div class="mx-auto max-w-5xl p-0 sm:px-6 sm:pt-6 lg:px-8">
        <x-slot name="title">
            {{ __('tag.scan') }}
        </x-slot>
        <x-card padding="p-0 sm:px-2 sm:py-5 md:px-4" rounded="rounded-none sm:rounded-lg">
            {{-- <p class="text-center py-2">Camera Initializing</p> --}}
            <!-- <video class="qr-scanner sm:rounded sm:shadow"> -->
            <!-- </video> -->
            <div x-data="{ imageUrl: '', isImageSelected: false }" class="preview-container w-1/2 h-128 border-2 border-dashed border-gray-300 flex items-center justify-center relative">
                <input type="file" id="file-input" accept="image/*" style="display: none;"
                    @change="if ($event.target.files[0]) { 
                        const reader = new FileReader(); 
                        reader.onload = e => { imageUrl = e.target.result; isImageSelected = true; }; 
                        reader.readAsDataURL($event.target.files[0]); 
                    }" />

                <label x-show="!isImageSelected" for="file-input" class="preview-text cursor-pointer z-10 text-center">Click to select an image</label>

                <button x-show="isImageSelected" @click="imageUrl = ''; isImageSelected = false" class="absolute top-2 right-2 bg-gray-500 text-white rounded-full flex items-center justify-center p-3">
                    <i class="fas fa-times"></i>
                </button>

                <img x-show="imageUrl" :src="imageUrl" class="preview-image absolute inset-0 w-full h-full object-cover" alt="Image Preview" />
            </div>
            <x-button icon="upload" primary label="{{ __('Upload') }}" class="my-2" />
            <p class="py-2 text-center">Scanning is done locally in your browser, nothing is sent to the server. The
                <x-button 2xs outline href="https://github.com/nimiq/qr-scanner" label="qr-scanner" /> JS library is used
                for this.
            </p>
        </x-card>
    </div>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                this.imageUrl = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>