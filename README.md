# NVIDIA-SMI to JSON

Display some information from the `nvidia-smi` **command** in JSON.

```yaml
version: '3.8'

services:
  php-apache:
    container_name: nvidia-smi
    image: php:8.2-apache
    ports:
      - 8080:80
    volumes:
      - .:/var/www/html
      - /usr/lib/x86_64-linux-gnu/libnvidia-ml.so:/usr/lib/x86_64-linux-gnu/libnvidia-ml.so
      - /usr/lib/x86_64-linux-gnu/libnvidia-ml.so.1:/usr/lib/x86_64-linux-gnu/libnvidia-ml.so.1
      - /usr/bin/nvidia-smi:/usr/bin/nvidia-smi
    environment:
      - NVIDIA_VISIBLE_DEVICES=all
      - NVIDIA_DRIVER_CAPABILITIES=all
    deploy:
      resources:
        reservations:
          devices:
            - driver: nvidia
              count: all
              capabilities: [gpu]
```
