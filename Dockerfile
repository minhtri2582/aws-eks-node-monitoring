FROM php:7.4-cli
RUN apt update \
  && apt install -y -f apt-transport-https \
		libicu-dev \
		libjpeg-dev \
		libfreetype6-dev \
		libonig-dev \
		libpng-dev \
		libpq-dev \
		libwebp-dev \
		libxml2-dev \
		libzip-dev \
		acl \
		cron \
		git \
		zip

RUN  docker-php-ext-install \
		bcmath \
		exif \
		gd \
		gettext \
		intl \
		mbstring \
		zip

RUN pecl install redis-5.1.1 \
	&& docker-php-ext-enable redis
# INSTALL KUBECTL
RUN curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
RUN install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl

#INSTALL AWS
RUN curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" && unzip awscliv2.zip && ./aws/install && rm -f awscliv2.zip

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

CMD ["/bin/bash", "-c", "aws eks --region ap-southeast-1 update-kubeconfig --name $EKS_CLUSTER;php eks_node_check.php"]