$(document).ready(function () {

    // Mountain selection
    $('input[name="mountain"]').on('change', function () {
        const selected = $(this).val();

        $('#selectedMountainText').text(selected);
    });

    // Individual / Group selection
    function updateParticipation() {

        const type = $('input[name="type"]:checked').val();

        if (type === 'Group') {
            $('#groupFields').slideDown(200);

            $('input[name="group_name"]').prop('required', true);
            $('input[name="group_size"]').prop('required', true);

        } else {
            $('#groupFields').slideUp(200);

            $('input[name="group_name"]').prop('required', false);
            $('input[name="group_size"]').prop('required', false);
        }
    }

    $('input[name="type"]').on('change', updateParticipation);

    updateParticipation();

    // Form validation
    $('#eventForm').on('submit', function (event) {

        const selectedMountain = $('input[name="mountain"]:checked').val();

        if (!selectedMountain) {
            event.preventDefault();

            alert('Please select a mountain before registration.');

            $('#destinations')[0].scrollIntoView({
                behavior: 'smooth'
            });

            return false;
        }

        const type = $('input[name="type"]:checked').val();

        if (type === 'Group') {
            const groupSize = parseInt($('input[name="group_size"]').val());

            if (!groupSize || groupSize < 2 || groupSize > 30) {
                event.preventDefault();
                alert('Group size must be between 2 and 30.');
                return false;
            }
        }

        if (!this.checkValidity()) {
            event.preventDefault();
            this.reportValidity();
            return false;
        }

    });

});
