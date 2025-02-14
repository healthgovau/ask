##
# @see  https://govcms.gov.au/wiki-advanced#docker
#

ARG CLI_IMAGE
ARG GOVCMS_IMAGE_VERSION=8.9.14

FROM ${CLI_IMAGE} as cli
FROM govcms/php:${GOVCMS_IMAGE_VERSION}

COPY --from=cli /app /app
