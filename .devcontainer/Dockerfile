FROM gitpod/workspace-mysql

USER root

RUN curl -fsSL https://apt.releases.hashicorp.com/gpg | apt-key add -
RUN apt-add-repository "deb [arch=amd64] https://apt.releases.hashicorp.com $(lsb_release -cs) main"
RUN apt-get update
RUN apt-get install rsync vault php-bz2 php-imagick -y
ENV APACHE_DOCROOT_IN_REPO=docroot

USER gitpod

#RUN echo '. $GITPOD_REPO_ROOT/.gitpod/.bash_aliases' >> ~/.bashrc

RUN composer global config minimum-stability dev
RUN composer global require drush/drush:^8 hirak/prestissimo --no-update
RUN composer global update
RUN echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc

RUN git config --global core.fileMode false