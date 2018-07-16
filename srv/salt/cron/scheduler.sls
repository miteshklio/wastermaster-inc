# Laravel command scheduler

laravel_scheduler:
  cron.present:
    - name: php /home/public_html/app/current/artisan schedule:run >> /dev/null 2>&1
    - user: root
    - minute: '*'
    - hour: '*'
    - daymonth: '*'
    - month: '*'
    - dayweek: '*'
