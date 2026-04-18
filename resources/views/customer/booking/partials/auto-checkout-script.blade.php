<script>
    (() => {
        const checkInInput = document.getElementById('guest_planned_check_in');
        const checkOutInput = document.getElementById('guest_planned_check_out');

        if (!checkInInput || !checkOutInput) {
            return;
        }

        const updateCheckoutFromCheckin = () => {
            const value = checkInInput.value;
            if (!value || !value.includes(':')) {
                return;
            }

            const [hourText, minuteText] = value.split(':');
            const hour = Number.parseInt(hourText, 10);
            const minute = Number.parseInt(minuteText, 10);
            if (Number.isNaN(hour) || Number.isNaN(minute)) {
                return;
            }

            const checkoutHour = (hour + 26) % 24;
            checkOutInput.value = `${String(checkoutHour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
        };

        checkInInput.addEventListener('change', updateCheckoutFromCheckin);
        checkInInput.addEventListener('input', updateCheckoutFromCheckin);
    })();
</script>
