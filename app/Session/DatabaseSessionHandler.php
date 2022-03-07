namespace App\Session;
class DatabaseSessionHandler extends \Illuminate\Session\DatabaseSessionHandler
{
    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        if ($this->exists) {
            $this->getQuery()->where('id', $sessionId)->update([
                'payload' => base64_encode($data), 'last_activity' => time(), 'user_id' => $user_id,
            ]);
        } else {
            $this->getQuery()->insert([
                'id' => $sessionId, 'payload' => base64_encode($data), 'last_activity' => time(), 'user_id' => $user_id,
            ]);
        }

        $this->exists = true;
    }
}