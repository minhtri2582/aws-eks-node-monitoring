FROM minhtri2582/aws-php-sdk-base:latest
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

# OPTIONAL pass to k8s CMD
CMD ["/bin/bash", "-c", "aws eks --region ap-southeast-1 update-kubeconfig --name $EKS_CLUSTER;php eks_node_check.php; php eks_pod_pending.php"]