FROM golang:1.16.6
# Arguments defined in docker-compose.yml
ARG user
ARG uid

WORKDIR ./var/www

# Install system dependencies
RUN apt-get update && apt-get upgrade -y
RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
RUN sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list'
RUN apt-get update
RUN apt-get install -y google-chrome-stable


RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       bash \
       gettext-base \
       supervisor \
    && rm -rf /var/lib/apt/lists/*


COPY ./golang/supervisord.conf /etc/supervisord.conf


COPY /golang .
RUN go mod download

CMD ["/usr/bin/supervisord", "-n"]

USER $user
