#!/bin/bash

rm -r export/
mkdir -p export/
git archive master | tar -x -C export/
rm export/configuration.php
rm export/shadow.php
rm export/*.sh
rm export/.gitignore

