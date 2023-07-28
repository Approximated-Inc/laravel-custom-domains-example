<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class Approximated {

    public $api_url = "https://cloud.approximated.app/api";


    /**
     * Create a virtual host.
     *
     * @param datatype $incoming_address The incoming address.
     * @param datatype $target_address The target address.
     * @param array $opts Optional fields. See https://approximated.app/docs/#create-virtual-host for more details.
     * @return array 
     * [
     *     "success" => true,
     *     "data" => [
     *         "id": 445922,
     *         "incoming_address": "acustomdomain.com",
     *         "target_address": "myapp.com",
     *         "target_ports": "443",
     *         "user_message": "In order to connect your domain, you'll need to have a DNS A record that points acustomdomain.com at..."
     *     ]
     * ]
     * @return array  
     * [
     *     "success" => false,
     *     "errors" => [
     *         "incoming_address" => [
     *                 "This incoming address has already been created on the reverse proxy server you selected.",
     *         ],
     *     ],
     * ]
     */
    public function create_vhost($incoming_address, $target_address, $opts = []) 
    {
        // see https://approximated.app/docs/#create-virtual-host for optional fields
        $data = array_merge($opts, [
                    'incoming_address' => $incoming_address,
                    'target_address' => $target_address,
                ]);
                
        $response =  Http::withHeaders(['api-key' => env('APPROXIMATED_API_KEY')])
                        ->post($this->api_url . "/vhosts", $data);


        return $this->handle_response($response);
    }

    /**
     * Updates a virtual host.
     * Any fields not passed into options will remain the same.
     *
     * @param datatype $current_incoming_address The current incoming address.
     * @param array $opts Optional fields. See https://approximated.app/docs/#update-virtual-host for more details.
     * @return array 
     * [
     *     "success" => true,
     *     "data" => [
     *          "apx_hit" => true, // requests are reaching the cluster
     *          "created_at" => "2023-04-03T17:59:28", // UTC timezone
     *          "dns_pointed_at" => "213.188.210.168", // DNS for the incoming_address
     *          "has_ssl" => true,
     *          "id" => 405455,
     *          "incoming_address" => "adifferentcustomdomain.com",
     *          "is_resolving" => true, // is this returning a response
     *          "last_monitored_humanized" => "1 hour ago",
     *          "last_monitored_unix": 1687194590,
     *          "ssl_active_from" => "2023-06-02T20:19:15", // UTC timezone
     *          "ssl_active_until" => "2023-08-31T20:19:14", // UTC timezone, auto-renews
     *          "status": "ACTIVE_SSL",
     *          "status_message" => "Active with SSL",
     *          "target_address" => "myapp.com",
     *          "target_ports" => "443"
     *     ]
     * ]
     * @return array  
     * [
     *     "success" => false,
     *     "errors" => [
     *         "incoming_address" => [
     *                 "This incoming address has already been created on the reverse proxy server you selected.",
     *         ],
     *     ],
     * ]
     */
    public function update_vhost($current_incoming_address, $opts = []) 
    {
        // see https://approximated.app/docs/#update-virtual-host for optional fields
        $data = array_merge($opts, [
                    'current_incoming_address' => $current_incoming_address
                ]);

        $response =  Http::withHeaders(['api-key' => env('APPROXIMATED_API_KEY')])
                        ->post($this->api_url . "/vhosts/update/by/incoming", $data);


        return $this->handle_response($response);
    }
    
    /**
     * Gets a virtual host.
     *
     * @param datatype $incoming_address The incoming address.
     * @return array 
     * [
     *     "data" => [
     *          "apx_hit" => true, // requests are reaching the cluster
     *          "created_at" => "2023-04-03T17:59:28", // UTC timezone
     *          "dns_pointed_at" => "213.188.210.168", // DNS for the incoming_address
     *          "has_ssl" => true,
     *          "id" => 405455,
     *          "incoming_address" => "adifferentcustomdomain.com",
     *          "is_resolving" => true, // is this returning a response
     *          "last_monitored_humanized" => "1 hour ago",
     *          "last_monitored_unix": 1687194590,
     *          "ssl_active_from" => "2023-06-02T20:19:15", // UTC timezone
     *          "ssl_active_until" => "2023-08-31T20:19:14", // UTC timezone, auto-renews
     *          "status": "ACTIVE_SSL",
     *          "status_message" => "Active with SSL",
     *          "target_address" => "myapp.com",
     *          "target_ports" => "443"
     *     ]
     * ]
     */
    public function get_vhost($incoming_address) 
    {
        $response =  Http::withHeaders(['api-key' => env('APPROXIMATED_API_KEY')])
                        ->get($this->api_url . "/vhosts/by/incoming/".$incoming_address);


        return $this->handle_response($response);
    }

    /**
     * Deletes a vhost by incoming address.
     *
     * @param mixed $incoming_address The incoming address of the vhost to be deleted.
     * @return array ["success" => true, "data" => null]
     */
    public function delete_vhost($incoming_address) 
    {
        $response =  Http::withHeaders(['api-key' => env('APPROXIMATED_API_KEY')])
                        ->delete($this->api_url . "/vhosts/by/incoming/".$incoming_address);


        return $this->handle_response($response);
    }

    private function handle_response($response) {
        // return success and the vhost data if successful
        if($response->successful()) {
            return array_merge(["success" => true], $response->json() || ["data" => null]);    
        }

        if($response->notFound()){
            return ["success" => false, "data" => "Not Found."];
        }

        // There's an issue with the data we sent (duplicate incoming address, missing fields, etc.)
        // returns success as false and the data will be the errors.
        if($response->unprocessableEntity()){
            return array_merge(["success" => false], $response->json());  
        }

        // otherwise throw if there was an error
        $response->throw();
    }
}
