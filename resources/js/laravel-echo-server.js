import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: "https://chatnexus.xyz:6001/"
});
