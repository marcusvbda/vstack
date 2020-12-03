import Echo from 'laravel-echo'
import Vue from 'vue'
if (laravel.chat) {
	if (laravel.chat.pusher_key) {
		window.Pusher = require('pusher-js')
		window.Echo = new Echo({
			broadcaster: 'pusher',
			key: laravel.chat.pusher_key,
			cluster: laravel.chat.pusher_cluster
		})
		Vue.prototype.$echo = window.Echo
	}
}
