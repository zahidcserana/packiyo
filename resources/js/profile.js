window.ProfileForm = function () {
    $(document).ready(function () {

        function previewProfileImage (input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    $("#preview-image").attr("src", e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
                $('#preview-image').removeClass('d-none');
            }
        }

        $("#preview-upload-profile-image").click(function(e) {
            e.preventDefault();
            $('#input-picture').trigger('click');
        });

        $("#input-picture").change(function(){
            previewProfileImage(this);
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $(e.target.closest('ul')).find('li').removeClass('active')
            e.target.closest('li').classList.add('active')
        })

        $('#manage-subscription-button').click(function () {
            $('#subscription-tab').addClass('d-none')
            $('#manage-subscription-tab').removeClass('d-none')
        })

        $('#cancellation-reason').on('input', function () {
            if (($(this).val())) {
                $('#remove-subscription').addClass('bg-logoOrange text-white')
            } else {
                $('#remove-subscription').removeClass('bg-logoOrange text-white')
            }
        })

        $('#back-to-current-subscription').click(function () {
            $('#subscription-tab').removeClass('d-none')
            $('#manage-subscription-tab').addClass('d-none')
        })
    });
};
