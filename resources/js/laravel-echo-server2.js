import Echo from 'laravel-echo';
window.Echo2 = new Echo({
    broadcaster: 'socket.io',
    host: "https://nexusapi.xyz:6001/"
});
