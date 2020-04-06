<template>
    <div class="container mx-auto text-white mb-8">
        <div>
            <div class="places-input text-gray-800">
                <input type="search" id="city" class="form-control w-full" placeholder="In which city do you live?" />
                <p>Selected: <strong id="address-value">none</strong></p>
            </div>
        </div>

        <p class="mt-4 text-indigo-900">Location: <strong class="text-2xl font-semibold">{{ location.name }}</strong></p>
        <div class="lg:flex mt-10">
            <div class="w-43 border-r border-b border-l border-gray-400 bg-gray-900 lg:border-l-0 lg:border-t lg:border-gray-400 bg-white rounded-lg lg:rounded-lg lg:rounded-lg p-4 px-8 pb-10 flex flex-col justify-between leading-normal">
                <div class="items-center">
                    <div class="text-2xl font-semibold">{{ toDayOfWeek(currentTemperature.time) }}</div>
                    <div class="font-semibold">{{ currentTemperature.actual }}°C</div>
                    <div>Feels like {{ currentTemperature.feels }}°C</div>
                
                    <div class="semi-bold">{{ currentTemperature.summary }} throughout the day.</div>
                </div>
                <div class="mt-8">
                    <canvas id="iconCurrent" width="54" height="54"></canvas>
                </div>
            </div> <!-- end of current weather -->

            <div v-for="(day, index) in daily" 
                    :key="day.time" 
                    class="flex items-center"
                    :class="{ 'mt-8' : index > 0 }"
                    v-if="index < 6"
            >
                <div class="w-43 border-r border-b border-l border-gray-800 bg-gray-900 lg:border-l-0 lg:border-t lg:border-gray-400 bg-white rounded-lg lg:rounded-lg lg:rounded-lg p-4 px-8 pb-10 flex flex-col justify-between leading-normal">
                    <div class="items-center">
                        <div class="text-2xl font-semibold">{{ toDayOfWeek(day.time) }}</div>
                        <div class="font-semibold mt-6">Max: {{ Math.round(day.temperatureHigh) }}°C</div>
                        <div class="font-semibold mb-4">Min: {{ Math.round(day.temperatureLow) }}°C</div>
                    
                        <div class="">{{ day.summary }}</div>
                    </div>
                    <div class="mt-8">
                        <canvas :id="`icon${index+1}`" :data-icon="day.icon" width="48" height="48"></canvas>
                    </div>
                </div>
            </div> <!-- end of weekly-weather -->
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

            var $address = document.querySelector('#address-value');
            placesAutocomplete.on('change', (e) => {
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
                    time: '',
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
                        this.currentTemperature.time = data.currently.time;
                        this.currentTemperature.actual = Math.round(data.currently.temperature);
                        this.currentTemperature.feels = Math.round(data.currently.apparentTemperature);
                        this.currentTemperature.summary = data.currently.summary;
                        this.currentTemperature.icon = data.currently.icon;

                        this.daily = data.daily.data;
                        this.hourly = data.hourly.data;
                        //console.log('daily data: ' + this.daily);
                        //console.log('Hourly data: ' + data.hourly.data);

                        skycons.add("iconCurrent", this.currentTemperature.icon);
                        skycons.play();

                        this.$nextTick(() => {
                            skycons.add('icon1', document.getElementById('icon1').getAttribute('data-icon'));
                            skycons.add('icon2', document.getElementById('icon2').getAttribute('data-icon'));
                            skycons.add('icon3', document.getElementById('icon3').getAttribute('data-icon'));
                            skycons.add('icon4', document.getElementById('icon4').getAttribute('data-icon'));
                            skycons.add('icon5', document.getElementById('icon5').getAttribute('data-icon'));
                            skycons.add('icon6', document.getElementById('icon6').getAttribute('data-icon'));
                            skycons.play();
                        })
                    })
            },

            toDayOfWeek(timestamp) {
                var newDate = new Date(timestamp * 1000);
                const days = ['MON','TUE','WED','THU','FRI','SAT','SUN'];

                return days[newDate.getDay()];
            }
        }
    }
</script>
