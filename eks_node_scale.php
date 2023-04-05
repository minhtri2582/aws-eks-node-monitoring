<?php
// aws eks update-nodegroup-config --cluster-name <cluster-name> --nodegroup-name <nodegroup-name> --scaling-config minSize=<min-size>,maxSize=<max-size>,desiredSize=<desired-size>
require "vendor/autoload.php";

use Aws\Eks\EksClient;

if (isset($argv[1])) {
    $desiredSize = 0;
    $maxSize = 1;
    $minSize = 0;
} else {
    $desiredSize = 3;
    $maxSize = 3;
    $minSize = 3;
}

$nodeGroups = getenv('NODE_GROUPS');
$nodeGroups = explode(',', $nodeGroups);

foreach ($nodeGroups as $nodegroupName){
    scaleNode($nodegroupName, 0, 1, 0);
}

function scaleNode($nodegroupName, $desiredSize, $maxSize, $minSize)
{
    try {
        $eksClient = new EksClient([
            'version' => 'latest',
            'region' => 'ap-southeast-1', // Replace with your region
        ]);

        $clusterName = getenv('EKS_CLUSTER');

        $result = $eksClient->updateNodegroupConfig([
            'clusterName' => $clusterName,
            'nodegroupName' => $nodegroupName,
            'scalingConfig' => [
                'desiredSize' => $desiredSize,
                'maxSize' => $maxSize,
                'minSize' => $minSize
            ],
        ]);
        if ($result->get('update')) {
            echo "Node group $nodegroupName update is in progress.\n";
        } else {
            echo "Node group $nodegroupName update failed.\n";
        }
    } catch (AwsException $e) {
        // Catch any AWS SDK errors
        echo $e->getMessage();
    } catch (Exception $e) {
        // Catch any other errors
        echo $e->getMessage();
    }
}