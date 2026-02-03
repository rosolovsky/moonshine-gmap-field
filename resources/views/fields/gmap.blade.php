<div x-data="map">
    <div class="z-0 w-full p-6 text-gray-900 md:w-1/2 h-96 rounded-md" id="map"></div>

    <x-moonshine::form.input type="hidden" name="{{ $column }}_lat" x-model="lat" />
    <x-moonshine::form.input type="hidden" name="{{ $column }}_lng" x-model="lon" />
    <p class="text-sm text-center text-gray-500 mt-2">
        Клікніть на карті або перемістіть маркер для встановлення координат
    </p>
</div>

<script>
    window.loadGoogleMaps = (() => {
        let promise

        return (apiKey = @js($apiKey), language = @js($language)) => {
            if (promise) return promise

            promise = new Promise((resolve, reject) => {
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                    resolve(window.google)
                    return
                }

                window.__initGoogleMaps = () => resolve(window.google)

                const script = document.createElement('script')
                script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&language=${language}&callback=__initGoogleMaps`
                script.async = true
                script.defer = true
                script.onerror = reject

                document.head.appendChild(script)
            })

            return promise
        }
    })()

    document.addEventListener('alpine:init', () => {
        Alpine.data('map', () => ({
            mapId: 'map',
            lat: @js($latitude ?? 0),
            lon: @js($longitude ?? 0),
            apiKey: @js($apiKey),
            language: @js($language),
            zoom: @js($zoom),
            minZoom: @js($minZoom),
            maxZoom: @js($maxZoom),
            map: null,
            marker: null,

            async init() {
                await this.initCoords()
                await loadGoogleMaps(this.apiKey, this.language)
                this.showMap()
            },

            async initCoords() {
                if (this.hasCoords()) return

                return new Promise((resolve) => {
                    if (!navigator.geolocation) {
                        this.lat = 0
                        this.lon = 0
                        return resolve()
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.lat = pos.coords.latitude
                            this.lon = pos.coords.longitude
                            resolve()
                        },
                        () => {
                            this.lat = 0
                            this.lon = 0
                            resolve()
                        }
                    )
                })
            },

            hasCoords() {
                return Number.isFinite(this.lat) && Number.isFinite(this.lon)
            },

            showMap() {
                const mapEl = document.getElementById(this.mapId)

                if (!mapEl) return

                this.map = new google.maps.Map(mapEl, {
                    center: { lat: this.lat, lng: this.lon },
                    zoom: this.zoom,
                    maxZoom: this.maxZoom,
                    minZoom: this.minZoom
                })

                this.marker = new google.maps.Marker({
                    position: { lat: this.lat, lng: this.lon },
                    map: this.map,
                    draggable: true
                })

                this.map.addListener('click', (e) => {
                    this.updatePosition(e.latLng.lat(), e.latLng.lng());
                });

                this.marker.addListener('dragend', (e) => {
                    this.updatePosition(e.latLng.lat(), e.latLng.lng());
                });
            },

            updatePosition(lat, lng) {
                this.lat = lat;
                this.lon = lng;

                if (this.marker) {
                    this.marker.setPosition({ lat: lat, lng: lng });
                }

                if (this.map) {
                    this.map.panTo({ lat: lat, lng: lng });
                }
            }
        }));
    });
</script>