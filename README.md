# Solute.de Billiger.de Connect Magento Extension
solute.de GmbH Zeppelinstraße 15 D-76185 Karlsruhe

# Installation

## Voraussetzung
- Magento Cron Funktionalität muss gewährleistet sein

## Kompatibiliät
- Magento CE >= 1.9 
- Ältere Versionen ab Version 1.6 sollten funktionieren, wurden bisher aber nicht getestet

## Composer
für die Composer Installation ist ein Serverseitiges allow symlinks
{
    "require":{
        "solutede/billigerde-connect":"dev-master",
    },
    "extra":{
        "magento-root-dir": "./",
        "auto-append-gitignore": true,
        "magento-deploystrategy": "symlink"
    }
}

## Download

https://bitbucket.org/solutede/billigerde-connect/downloads/ 
Zip Datei in Magento Root Directory entpacken (alles in dem Verzeichnis wo auch der das Verzeichnis app/ ist!)

## weitere Maßnahmen
- nach der Installation ist das Leeren des Caches erforderlich und eine Ab- und Anmeldung im Adminbereich

## Cron
- damit die Exporte immer wieder angestoßen werden, gibt es einen Crontab Eintrag

### Cron Eintrag ändern
#### über config.xml
- app/code/local/Billigerde/Connect/etc/config.xml
- zu finden ganz unten ("<crontab>")
- Zeile <cron_expr>10 * * * *</cron_expr> bedeuted jede Stunde zur 10ten Minute
- <cron_expr>15 */2 * * *</cron_expr> alle 2 Stunden zur 15ten Minute

#### Module für Crons (falls man die config.xml nicht bearbeiten möchte)
- AOE Scheduler https://www.magentocommerce.com/magento-connect/aoe-scheduler.html

## Konfiguration

System > Konfiguration > Solutede > Billigerde Connect

- General 
    - ShopId :: Die Shop Id von Ihrem Store bei Billiger.de
- Tracking 
    - Methode :: Auschalten oder Normales Tracking oder erweitertes Tracking
- Export Einstellungen Storeweise einzustellen
    - Aktiv :: An oder Aus
    - Debugging :: Aus oder entsprechende DebugLevel. Extra Dateien werden unter var/log/ geschrieben
    - Csv Spaltentrennzeichen 
    - Csv Kapselungszeichen
    - Csv Datei Name :: Wenn mehrere Stores exportieren, sollten sich die Dateinamen unterscheiden
    - Csv Datei Pfad :: Der Pfad zu der Datei
    - Produkt Kollektion Größe Pro Step :: Anzahl der Produkte die mit einmal geladen werden
    - Kategoriefilter Einschluss :: Filter optional zum eingrenzen von Produkten die exportiert werden sollen nach Kategorien
    - Kategoriefilter Ausschluss :: Filter optional zum ausgrenzen von Produkten die nicht exportiert werden sollen nach Kategorien
    - Sichtbarkeitsfilter :: Filter optional zum eingrenzen der Produkte nach Sichtbarkeit
    - Exportiere deaktivierte Produkte :: Filter für deaktivierte Produkte
    - Exportiere Nicht Auf Lager Produkte :: Filter für nicht auf Lager Produkte
 
- Profileinstellung 
    - Mapping von Csv Feldern zu Attributen oder anderen Werten
    - Field sind von Billiger.de vordefinierte Spaltenkennzeichnungen. 
    - Field-Bedeutungen sind unter den Leitfäden von https://www.billiger.de/application/tech.html einsehbar
    - DropDowns für Mapping Hydrating und Formatting sind via Event erweiterbar siehe Events
    - Nicht gesetzte Mappings führen zu nicht Export der Spalte
    - Mapping sind AttributeValues oder GetterValue Mappings zu den CsvFeldern
    - Hydratings sind für komplexere Values in der Csv gedacht e.g. CategoryTreeString
    - Formattings sind die auf den sich ergebenden Value angewendeten Formatierungen für die Csv

 
# Funktionsweise
## Events
- billigerde_connect_fetch_mappings 
:: Zum Erweitern oder Überschreiben der Mappings. Ermöglicht eigene Getter oder andere Mappingarten hinzuzufügen.
 
- billigerde_connect_fetch_formatters
:: Zum Erweitern oder Überschreiben der Formatierer. 
 
- billigerde_connect_fetch_hydrators
:: Zum Erweitern oder Überschreiben der Hydratoren.
 
- billigerde_connect_map_field
:: Zum Erweitern oder Überschreiben der Mapping Fields aus der Config.
 
- billigerde_connect_apply_product_collection_filter
:: Zum Erweitern oder Überschreiben der angewendeten Filter auf der Collection. 
 
- billigerde_connect_get_product_field_value
:: Zum Erweitern oder Überschreiben des Ergebnisses eines Product Values in der Csv


# Debugging

 - In der Systemkonfiguration sind Debuglevel einstellbar. Diese schreiben Debug Dateien unter var/log/
 - Im Normalbetrieb nicht eingeschaltet lassen!

# TroubleShooting

## Profileconfig auf default zurückstellen
- in der app/code/etc/config.xml unter default/billigerde_connect_setting/profile_config_default ist die Default Einstellung kopierbar und für den path 'billigerde_connect_setting/export/profile_config' in die Tabelle core_config_data zu Verfügung gestellt.
 




