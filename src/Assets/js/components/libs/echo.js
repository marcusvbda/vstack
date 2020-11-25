import Echo from 'laravel-echo'
if (laravel.chat) {
    if (laravel.chat.pusher_key) {
        window.Pusher = require('pusher-js')
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: laravel.chat.pusher_key,
            cluster: laravel.chat.pusher_cluster
        })
    }
}