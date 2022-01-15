FROM nginx

# Set upstream conf and remove the default conf
RUN echo "upstream php-upstream { server php-fpm:9000; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

COPY sites/web.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
