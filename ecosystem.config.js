module.exports = {
  apps: [
    {
      name: 'v-face-queue-worker',
      script: 'artisan',
      interpreter: 'php',
      args: ['queue:work', '--timeout=0'],
      instances: 1,
      exec_mode: 'fork',
    },
  ],
};
