FROM node:latest

# USER node

RUN mkdir /home/node/.npm-global
ENV PATH=/home/node/.npm-global/bin:$PATH
ENV NPM_CONFIG_PREFIX=/home/node/.npm-global
ENV HOME=/var/www/frontend

WORKDIR $HOME

COPY package*.json ${HOME}
RUN npm i -g npm
RUN npm install -g @angular/cli && npm cache clean --force
RUN npm install --verbose

EXPOSE 4200 49153 

CMD ["ng", "serve", "--host", "0.0.0.0", "--port", "4200", "--poll", "1" ]
# ENTRYPOINT ["tail", "-f", "/dev/null"]