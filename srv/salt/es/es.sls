install-java:
  pkg:
    - installed
    - name: default-jre

elasticsearch-key:
  cmd.run:
    - name: wget -O - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
    - require:
      - pkg: install-java

elasticsearch-repo:
  cmd.run:
    - name: echo 'deb http://packages.elastic.co/elasticsearch/1.7/debian stable main' | sudo tee -a /etc/apt/sources.list.d/elasticsearch-1.7.list && sudo apt-get update
    - require:
      - cmd: elasticsearch-key

install-elasticsearch:
  pkg.installed:
    - name: elasticsearch
    - require:
      - cmd: elasticsearch-repo

elasticsearch-rcd:
  cmd:
    - run
    - name: update-rc.d elasticsearch defaults
    - require:
      - pkg: install-elasticsearch