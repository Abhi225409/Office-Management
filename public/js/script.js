$(document).ready(function () {
    var consumedTimers = {}; // Object to hold consumed time (count up) in seconds for each task
    var remainingTimers = {}; // Object to hold remaining time (count down) in seconds for each task
    var intervals = {}; // Object to hold interval IDs for each checkbox

    // Function to save the task timer via AJAX
    function save_task_timer(taskId, consumed_hours, reamining_hours) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "/saveHours", // Laravel route for saving
            method: "POST",
            data: {
                task_id: taskId,
                consumed_hours: consumed_hours,
                reamining_hours: reamining_hours
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                } else {
                    alert("Failed to save task timer!");
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert("An error occurred while saving the task timer.");
            }
        });
    }

    // Function to format time from seconds to hh:mm:ss
    function formatTime(seconds) {
        let hrs = Math.floor(seconds / 3600);
        let mins = Math.floor((seconds % 3600) / 60);
        let secs = seconds % 60;
        return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    // Function to convert time (hh:mm:ss) into total seconds
    function timeToSeconds(time) {
        var parts = time.split(':');
        return (+parts[0] * 3600) + (+parts[1] * 60) + (+parts[2]);
    }


    // Load checkbox and timer states from localStorage on page load
    $('.task-checkbox').each(function () {
        var taskId = $(this).data('task-id');
        var assigned_hours = $('#assigned_hours_' + taskId).text();
        var lasttimerVlue = 3600 * assigned_hours;
        var isChecked = localStorage.getItem('checkbox_' + taskId);
        var savedConsumed = localStorage.getItem('consumed_time_' + taskId);
        var savedRemaining = localStorage.getItem('remaining_time_' + taskId);
        var isDisabled = localStorage.getItem('checkbox_disabled_' + taskId); // Check if the checkbox is disabled


        // If the task is disabled, permanently disable the checkbox
        if (isDisabled === 'true') {
            $(this).prop('disabled', true);
            return;
        }

        if (isChecked === 'true') {
            $(this).prop('checked', true);
            $('.task-checkbox').not(this).prop('disabled', true);

            // Retrieve saved timer values or default to 0 consumed and 4 hours remaining
            consumedTimers[taskId] = savedConsumed ? parseInt(savedConsumed) : 0;
            remainingTimers[taskId] = savedRemaining ? parseInt(savedRemaining) : lasttimerVlue;

            // Update UI with saved times
            $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
            $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

            // Resume the interval
            intervals[taskId] = setInterval(function () {
                consumedTimers[taskId] += 1;
                remainingTimers[taskId] -= 1;

                $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
                $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

                // Save the updated timer values to localStorage
                localStorage.setItem('consumed_time_' + taskId, consumedTimers[taskId]);
                localStorage.setItem('remaining_time_' + taskId, remainingTimers[taskId]);

                // Stop the timer when limits are reached
                if (consumedTimers[taskId] >= lasttimerVlue || remainingTimers[taskId] <= 0) {
                    clearInterval(intervals[taskId]);
                    alert("Timer completed for task " + taskId);

                    // Disable the checkbox and save its state
                    $('.task-checkbox[data-task-id="' + taskId + '"]').prop('disabled', true);
                    localStorage.setItem('checkbox_disabled_' + taskId, 'true');

                    // Save the disabled state via AJAX
                    save_task_timer(taskId, formatTime(consumedTimers[taskId]), formatTime(remainingTimers[taskId]), true);
                }
            }, 1000); // Update every 1 second
        }
    });

    // Handle checkbox state changes
    $('.task-checkbox').on('change', function () {
        var taskId = $(this).data('task-id');
        var consumed_hours = $('#consumed_hours_' + taskId).text();
        var reamining_hours = $('#remaining_hours_' + taskId).text();
        var assigned_hours = $('#assigned_hours_' + taskId).text();
        var lasttimerVlue = 3600 * assigned_hours;

        if ($(this).is(':checked')) {
            // Save state to localStorage
            localStorage.setItem('checkbox_' + taskId, 'true');

            if (!consumedTimers[taskId]) {
                consumedTimers[taskId] = timeToSeconds(consumed_hours); // Start from saved or 0
            }

            if (!remainingTimers[taskId]) {
                remainingTimers[taskId] = timeToSeconds(reamining_hours); // Start from saved or 4 hours
            }

            // Start the interval
            intervals[taskId] = setInterval(function () {
                consumedTimers[taskId] += 1;
                remainingTimers[taskId] -= 1;

                $('#consumed_hours_' + taskId).text(formatTime(consumedTimers[taskId]));
                $('#remaining_hours_' + taskId).text(formatTime(remainingTimers[taskId]));

                // Save the updated timer values to localStorage
                localStorage.setItem('consumed_time_' + taskId, consumedTimers[taskId]);
                localStorage.setItem('remaining_time_' + taskId, remainingTimers[taskId]);

                // Stop the timer when limits are reached
                if (consumedTimers[taskId] >= lasttimerVlue || remainingTimers[taskId] <= 0) {
                    clearInterval(intervals[taskId]);
                    alert("Timer completed for task " + taskId);

                     // Disable the checkbox and save its state
                     $('.task-checkbox[data-task-id="' + taskId + '"]').prop('disabled', true);
                     localStorage.setItem('checkbox_disabled_' + taskId, 'true');
 
                     // Save the disabled state via AJAX
                     save_task_timer(taskId, formatTime(consumedTimers[taskId]), formatTime(remainingTimers[taskId]), true);
                }
            }, 1000);

            // Disable all other checkboxes
            $('.task-checkbox').not(this).prop('disabled', true);
        } else {
            // Remove state from localStorage
            localStorage.removeItem('checkbox_' + taskId);
            localStorage.removeItem('consumed_time_' + taskId);
            localStorage.removeItem('remaining_time_' + taskId);

            clearInterval(intervals[taskId]);

            // Re-enable all checkboxes if not disabled
            $('.task-checkbox').each(function () {
                var isDisabled = localStorage.getItem('checkbox_disabled_' + $(this).data('task-id'));
                if (isDisabled !== 'true') {
                    $(this).prop('disabled', false);
                }
            });

            // Save timer values via AJAX
            save_task_timer(taskId, consumed_hours, reamining_hours);
        }
    });

    setTimeout(function () {
        $(".alert").addClass("d-none");
    }, 3000);
});

