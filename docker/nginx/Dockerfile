# Build mix files
FROM node:14-alpine as build-stage
WORKDIR /app
COPY ./package.json /app
COPY ./package-lock.json /app
RUN npm install
COPY ./ /app
RUN npm run production

# Only copy built-files and serve it
FROM nginx:1.14.2-alpine as deploy-stage
COPY ./docker/nginx/vface.conf /etc/nginx/conf.d/default.conf
COPY --from=build-stage /app /usr/share/nginx/html