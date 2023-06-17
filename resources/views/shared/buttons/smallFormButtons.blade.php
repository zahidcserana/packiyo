<div class="d-flex align-items-center smallButtonsContainer">
    <div class="editFormContent d-flex align-items-center">
        <i class="picon-edit-filled icon-orange icon-lg" title="Edit"></i>
    </div>
    <div class="d-flex align-items-center ml-3">
        <button class="saveButton mr-3 p-0 border-0 bg-white d-none align-items-center" id="{{ $saveButtonId ?? '' }}" type="button">
            <i class="picon-save-light icon-orange icon-lg" title="Save"></i>
        </button>
        <div class="d-flex align-items-center">
            <svg class="d-none loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <circle cx="50" cy="50" fill="none" stroke="#f7860b" stroke-width="10" r="45" stroke-dasharray="164.93361431346415 56.97787143782138">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                </circle>
            </svg>
            <div class="w4rAnimated_checkmark d-flex">
                <i class="d-none saveSuccess picon-check-circled-light icon-orange icon-lg"></i>
                <i class="d-none saveError picon-close-circled-light icon-orange icon-lg"></i>
            </div>
        </div>
    </div>
</div>
