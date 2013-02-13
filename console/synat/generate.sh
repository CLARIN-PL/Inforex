#!/bin/bash
#script to generate training/testing data for Liner2
#parameters:
#$1 - file with random-ordered list of iob files

#directory with the corpus
kpwr_dir=/home/kotu/Desktop/liner2_acl_2013/kpwr-1.1-disamb
#directory with important tools (at this place this script should work properly)
inforex_dir=/home/kotu/projects/inforex_web/console/synat
#name of the workspace
fold_dir=kpwr_folds_20130212
#the number of folds to generate
fold_number=10
fold_size=0
#clean current workspace
rm -rf $fold_dir
#create workspace and output directories
mkdir -p $fold_dir/ccl
mkdir -p $fold_dir/iob
#directory with final (important) result
mkdir -p $fold_dir/folds
#copy interesting ccl files from corpus directory
for file in `cat $kpwr_dir/index_names.txt`
do
    cp $kpwr_dir/$file $fold_dir/ccl/
done
#convert files to IOB format
php ccl2iob.php -i $fold_dir/ccl -o $fold_dir/iob -s nam
#create the index sorted by name
ls $fold_dir/iob | sort > $fold_dir/index_iob.txt
#if the random ordered index file is given as a parameter, copy it to $fold_dir
if [[ -n "$1" ]]
then
	cp $1 $fold_dir/index_iob_random.txt
else
	#create the index with a random order
	ls $fold_dir/iob | shuf > $fold_dir/index_iob_random.txt
fi
#get the number of files
file_number=$(cat $fold_dir/index_iob.txt | wc -l)
#get the approximate number of files for each fold
((fold_size = file_number / fold_number))
fold_id=0
line_number=0
#get the header of any IOB file
first_line=$(head -n 1 $fold_dir/index_iob.txt | xargs -I{} head -n 1 $fold_dir/iob/{})
#split all files (using random-ordered index) into separate source fold lists
for file_name in $(cat $fold_dir/index_iob_random.txt)
do
    if (( line_number % fold_size == 0 && fold_id < fold_number)) 
    then
        ((fold_id = fold_id + 1))
        #rm -f $fold_dir/fold-$fold_id.txt  
    fi
    ((line_number = line_number + 1))
    echo iob/$file_name >> $fold_dir/fold-$fold_id.txt
done
#merge the file lists in the appropriate training(fold_number - 1)/test(1) folds 
for ((i = 1; i <= fold_number; i++))
do
    rm -f $fold_dir/fold-$i.train.txt
    for ((j = 1; j <= fold_number; j++))
    do
        if (( i == j)) 
        then   
            rm -f $fold_dir/fold-$i.test.txt
            cat $fold_dir/fold-$j.txt >> $fold_dir/fold-$i.test.txt
        else
            cat $fold_dir/fold-$j.txt >> $fold_dir/fold-$i.train.txt
        fi     
    done
done
#merge all IOB files and generate output training/test folds
corpus_name=kpwr-1.1
for ((i = 1; i <= fold_number; i++))
do
    for file_type in 'train' 'test'
    do
        echo $first_line > $fold_dir/folds/$corpus_name.iob.fold-$i.$file_type
        for file_name in $(cat $fold_dir/fold-$i.$file_type.txt)            
        do        
            sed 1d $fold_dir/$file_name >> $fold_dir/folds/$corpus_name.iob.fold-$i.$file_type
        done
    done
done

