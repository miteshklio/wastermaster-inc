<script>
    var cities = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: ['Chicago', 'Minneapolis'],
        remote: {
            url: '/ajax/cities/autocomplete?query=%QUERY',
            wildcard: '%QUERY',
            filter: function (data) {
                return $.map(data.cities, function (city) {
                    return {
                        value: city
                    };
                });
            }
        }
    });

    $('.typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 1,
    },{
        name: 'cities',
        source: cities,
        display: 'value',
        limit: 100
    });
</script>
