<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxEftConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ebanx_eft';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'ebanx' => [
                    'availableBanks' => [
                        'banco_agrario' => 'Banco Agrario',
                        'banco_av_villas' => 'Banco AV Villas',
                        'banco_bbva_colombia_s.a.' => 'Banco BBVA Colombia',
                        'banco_caja_social' => 'Banco Caja Social',
                        'banco_colpatria' => 'Banco Colpatria',
                        'banco_cooperativo_coopcentral' => 'Banco Cooperativo Coopcentral',
                        'banco_corpbanca_s.a' => 'Banco CorpBanca Colombia',
                        'banco_davivienda' => 'Banco Davivienda',
                        'banco_de_bogota' => 'Banco de Bogotá',
                        'banco_de_occidente' => 'Banco de Occidente',
                        'banco_falabella_' => 'Banco Falabella',
                        'banco_gnb_sudameris' => 'Banco GNB Sudameris',
                        'banco_pichincha_s.a.' => 'Banco Pichincha',
                        'banco_popular' => 'Banco Popular',
                        'banco_procredit' => 'Banco ProCredit',
                        'bancolombia' => 'Bancolombia',
                        'bancoomeva_s.a.' => 'Bancoomeva',
                        'citibank_' => 'Citibank',
                        'helm_bank_s.a.' => 'Helm Bank',
                    ],
                ]
            ]
        ];
    }
}
