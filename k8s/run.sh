# RUN docker
docker run --env-file ../.env minhtri2582/aws-eks-node-monitoring:latest

# Create k8s secret from .env
kubectl create secret generic check-node-status-secret --from-env-file=../.env
kubectl apply -f eks_deploy.yaml
