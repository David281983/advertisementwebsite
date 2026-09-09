(function () {
    const input = document.getElementById('location-search');
    if (!input) return;

    const list  = document.getElementById('location-suggestions');
    const fName = document.getElementById('advertisement_locationName');
    const fLat  = document.getElementById('advertisement_latitude');
    const fLon  = document.getElementById('advertisement_longitude');

    input.value = fName.value;
    let timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);

        if (input.value.length < 3) {
            list.classList.add('hidden');
            return;
        }

        timer = setTimeout(async function () {
            const url = 'https://photon.komoot.io/api/?limit=5&q=' + encodeURIComponent(input.value);
            const data = await (await fetch(url)).json();

            list.innerHTML = '';

            data.features.forEach(function (place) {
                const p = place.properties;
                const name = [p.name, p.county, p.country].filter(Boolean).join(', ');
                const lon = place.geometry.coordinates[0];
                const lat = place.geometry.coordinates[1];

                const li = document.createElement('li');
                li.textContent = name;
                li.className = 'p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700';
                li.addEventListener('click', function () {
                    input.value = name;
                    fName.value = name;
                    fLat.value  = lat;
                    fLon.value  = lon;
                    list.classList.add('hidden');
                });
                list.appendChild(li);
            });

            list.classList.toggle('hidden', data.features.length === 0);
        }, 300);
    });
})();