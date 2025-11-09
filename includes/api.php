<?php
if (!defined('ABSPATH')) exit;

class JDS_API {

    private $namespace = 'jogo-dos-dedinhos/v1';
    private $table_name;
    private $top_limit = 5;

    public function __construct() {
        global $wpdb;
        $this->table_name = JOGO_DEDINHOS_TABLE;
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/post', array(
            'methods' => 'POST',
            'callback' => array( $this, 'handle_post_score' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( $this->namespace, '/get', array(
            'methods' => 'GET',
            'callback' => array( $this, 'handle_get_top_scores' ),
            'permission_callback' => '__return_true',
        ));
    }

    public function handle_post_score( $request ) {
        global $wpdb;

        $name  = sanitize_text_field( $request->get_param('name') );
        $score = intval( $request->get_param('score') );
        $ip    = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );

        if ( empty($name) || $score <= 0 ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Dados inválidos.' ), 400 );
        }

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name" );
        $min_score = $wpdb->get_var( "SELECT MIN(score) FROM $this->table_name" );

        $is_eligible = false;
        
        if ( $count < $this->top_limit ) {
            $is_eligible = true;
        } elseif ( $score > $min_score ) {
            $is_eligible = true;
        }

        if ( $is_eligible ) {
            $inserted = $wpdb->insert(
                $this->table_name,
                array( 'name' => $name, 'score' => $score, 'ip' => $ip ),
                array( '%s', '%d', '%s' )
            );

            if ( $inserted === false ) {
                return new WP_REST_Response( array( 'success' => false, 'message' => 'Erro ao salvar a pontuação.' ), 500 );
            }

            if ( $count >= $this->top_limit ) {
                $id_to_delete = $wpdb->get_var( "SELECT id FROM $this->table_name ORDER BY score ASC, id ASC LIMIT 1" );
                if ( $id_to_delete ) {
                    $wpdb->delete( $this->table_name, array( 'id' => $id_to_delete ), array( '%d' ) );
                }
            }

            return new WP_REST_Response( array( 'success' => true, 'message' => 'Pontuação salva no Top 5!' ), 200 );

        } else {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Sua pontuação não é alta o suficiente para entrar no Top 5.' ), 200 );
        }
    }

    public function handle_get_top_scores() {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT name, score, created_at FROM $this->table_name ORDER BY score DESC LIMIT %d",
                $this->top_limit
            )
        );

        if ( $results ) {
            return new WP_REST_Response( $results, 200 );
        } else {
            return new WP_REST_Response( array(), 200 );
        }
    }
}