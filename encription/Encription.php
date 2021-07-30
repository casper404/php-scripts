<?php
/**
 * Created by PhpStorm.
 * Date: 22.07.2021
 * Time: 13:11
 */



/**
 * Class EncryptionVigenere
 *
 * @see https://en.wikipedia.org/wiki/Vigen%C3%A8re_cipher
 */
class EncryptionVigenere
{
    private $alphabet = array(
        'A', 'a', 'B', 'b', 'C', 'c', 'D', 'd', 'E', 'e', 'F', 'f', 'G', 'g', 'H', 'h', 'I', 'i',
        'J', 'j', 'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P', 'p', 'Q', 'q', 'R', 'r',
        'S', 's', 'T', 't', 'U', 'u', 'V', 'v', 'W', 'w', 'X', 'x', 'Y', 'y', 'Z', 'z',
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
    );

    private $secret;
    private $offset_map = array();
    public $in_text;
    public $out_text;
    public $mode;
    public $available_modes = array('encode', 'decode');
    public $critical_uncode_vol = 0.2;

    /**
     * EncryptionVigenere constructor.
     *
     * @param string $secret
     * @param string $in_text
     * @param string $mode
     */
    public function __construct(string $secret, string $in_text, string $mode)
    {

        $this->secret = $secret;
        $this->in_text = $in_text;
        $this->mode = $mode;

        if(empty($this->in_text) or empty($this->secret) or !in_array($this->mode, $this->available_modes)) {
            trigger_error('Init error!', E_USER_ERROR);
        }

        //generate offset map
        foreach (str_split($this->secret) as $value) {
            if(!in_array($value, $this->alphabet)) {
                trigger_error('You can use in [secret] only english symbols or digits!', E_USER_ERROR);
            }
            $k = array_search ($value, $this->alphabet);
            $this->offset_map[] = $k;
        }

        //check symbols before process
        $loss_symbols = [];
        foreach (str_split($this->in_text) as $value) {
            if(!in_array($value, $this->alphabet)) {
                $loss_symbols[] = $value;
            }
        }

        $un_loss_symbols = array_unique($loss_symbols);

        if(count($un_loss_symbols) > 0 ) {
            echo "These characters will not be processed".PHP_EOL;
            print_r($un_loss_symbols);

            if (count($un_loss_symbols) / strlen($this->in_text) > $this->critical_uncode_vol) {
                trigger_error('Critical uncode volume!', E_USER_ERROR);
            }
        }
    }


    /**
     * @return string
     */
    public function run(): string
    {
        $secret_step = 0;
        $offset_correction = 0;
        $summary_offset = 0;

        foreach (str_split($this->in_text) as $current_symbol) {
            if(array_key_exists($secret_step, $this->offset_map)) {
                $offset = $this->offset_map[$secret_step];
            } else {
                $offset = 0;
                $secret_step = 0;
            }

            if(in_array($current_symbol, $this->alphabet)) {
                $current_position = array_search ($current_symbol, $this->alphabet);
                if($this->mode == 'encode') {
                    $summary_offset = $current_position + $offset;
                    $offset_correction = $summary_offset - count($this->alphabet);
                } elseif ($this->mode == 'decode') {
                    $summary_offset = $current_position - $offset;
                    $offset_correction = count($this->alphabet) + $summary_offset;
                }

                $offset_position = (array_key_exists($summary_offset, $this->alphabet)) ? $summary_offset : $offset_correction;
                $new_symbol = $this->alphabet[$offset_position];
                $this->out_text .= $new_symbol;
            } else {
                $this->out_text .= $current_symbol;
            }


            $secret_step += 1;
        }


        if(strlen($this->in_text) != strlen($this->out_text)) {
            trigger_error('Different length of texts!', E_USER_ERROR);
        }

        return $this->out_text;

    }
}