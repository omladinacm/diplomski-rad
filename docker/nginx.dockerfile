FROM nginx:1.21.3

ADD nginx/nginx.conf /etc/nginx/nginx.conf

RUN rm /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html