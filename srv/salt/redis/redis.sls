# Config

redis-server:
  pkg.installed:
    - name: redis-server
  service:
    - running
    - enable: True
    - name: redis-server
    - require:
      - pkg: redis-server

redis-restart:
  cmd.run:
    - name: sudo service redis-server restart
    - require:
      - pkg:  redis-server