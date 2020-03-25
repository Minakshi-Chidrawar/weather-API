<template>
    <div class="text-white mb-8">
        <div class="places-input text-gray-800">
            <input type="search" id="city" class="form-control w-full" placeholder="In which city do you live?" />
            <p>Selected: <strong id="address-value">none</strong></p>
        </div>
        <div class="weather-container font-sans w-50 max-w-lg overflow-hidden rounded-lg bg-gray-900 shadow-lg mt-4">
            <div class="current-weather flex items-center justify-between pl-12">
                <div class="flex-1 items-center my-6 ">
                    <div class="semi-bold">{{ location.name }}</div>
                    <div class="text-6xl font-semibold">{{ currentTemperature.actual }}°C</div>
                    <div>Feels like {{ currentTemperature.feels }} °C</div>
                    <div class="mb-8 semi-bold">{{ currentTemperature.summary }}</div>

                    <canvas id="iconCurrent" width="96" height="96" class="pt-4"></canvas>
                </div>
            </div> <!-- end of current weather -->            
        </div> <!-- end of weather container -->
        <div>
            Hourly weather
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchData();

            var placesAutocomplete = places({
                appId: 'plWOPD6TRYZA',
                apiKey: '49159ff5ef4ffa47c17fb5a6a66e3ff0',
                container: document.querySelector('#city'),
                templates: {
                    value: function(suggestion) {
                        return suggestion.name;
                    }
                }
            }).configure({
                type: 'city',
                aroundLatLngViaIP: false,
            });

            var $address = document.querySelector('#address-value')
            placesAutocomplete.on('change', (e) => {
                console.log(e.suggestion.name);
                $address.textContent = e.suggestion.value;

                this.location.name = `${e.suggestion.name}, ${e.suggestion.country}`;
                this.location.lat = e.suggestion.latlng.lat;
                this.location.lng = e.suggestion.latlng.lng;
            });

            placesAutocomplete.on('clear', function() {
                $address.textContent = 'none';
            });
        },

        watch: {
            location: {
                handler(newValue, oldValue) {
                    this.fetchData()
                },
                deep: true
            }
        },

        data() {
            return {
                currentTemperature: {
                    actual: '',
                    feels: '',
                    summary: '',
                    icon: ''
                },

                daily: [],

                location: {
                    name: 'London, UK',
                    lat: 51.5074,
                    lng: 0.1278
                }
            }
        },

        methods: {
            fetchData() {
                var skycons = new Skycons({'color': 'white'});

                fetch(`/api/weather?lat=${this.location.lat}&lng=${this.location.lng}`)
                    .then(response => response.json())
                    .then(data => {
                         console.log(data);
                        this.currentTemperature.actual = Math.round(data.currently.temperature);
                        this.currentTemperature.feels = Math.round(data.currently.apparentTemperature);
                        this.currentTemperature.summary = data.currently.summary;
                        this.currentTemperature.icon = data.currently.icon;

                        this.daily = data.daily.data;

                        skycons.add("iconCurrent", this.currentTemperature.icon);
                        skycons.play();

                        this.$nextTick(() => {
                            skycons.add('icon1', document.getElementById('icon1').getAttribute('data-icon'));
                            skycons.add('icon2', document.getElementById('icon2').getAttribute('data-icon'));
                            skycons.add('icon3', document.getElementById('icon3').getAttribute('data-icon'));
                            skycons.add('icon4', document.getElementById('icon4').getAttribute('data-icon'));
                            skycons.add('icon5', document.getElementById('icon5').getAttribute('data-icon'));
                            skycons.play();
                        })
                    })
            },

            toDayOfWeek(timestamp) {
                var newDate = new Date(timestamp * 1000);
                const days = ['SUN','MON','TUE','WED','THU','FRI','SAT'];

                return days[newDate.getDay()]
            }
        }
    }
</script>
