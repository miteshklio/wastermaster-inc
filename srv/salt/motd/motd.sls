 /etc/motd.tail:
  file.managed:
    - source: salt://motd/files/motd.tail