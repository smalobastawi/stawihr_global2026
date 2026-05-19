import Echo from 'laravel-echo';

window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

const employeeId = document.head.querySelector('meta[name="employee-id"]').content;

if (employeeId) {
    window.Echo.private(`approver.${employeeId}`)
        .listen('.LeaveApplicationEvent', (data) => {
            console.log('New leave application:', data);
            // Show notification to user
            showNotification(data);
        });
}

function showNotification(data) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Leave Approval Needed', {
            body: data.message,
            icon: '/icon.png'
        });
    }
    // Or use a toast notification library
}