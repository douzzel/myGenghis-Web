<?php  
  
/*************************************************************************** 
  *   begin                : Saturday, Feb 13, 2001 
 *   copyright            : (C) 2001 The phpBB Group 
 *   email                : <!-- e --><a href="mailto:support@phpbb.com">support@phpbb.com</a><!-- e --> 
 
 *   This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by 
 *   the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 
  
 * Template class. By Nathan Codding of the phpBB group. The interface was originally inspired by PHPLib <span class="posthilit">templates</span>, and the template file formats are quite similar. 
 ***************************************************************************/  
  
class Template {

	//sous dossier de r�f�rence, si besoin
	private static $subRootDir = '';
	public static function setSubRootDir($d) {
		self::$subRootDir = $d;
	}
  
    /* 
    Les fonctions publiques de cette <span class="posthilit">classe</span> sont :  
        - setFiles([tableau associatif sous la forme 'surnom' => 'nom r�el du fichier']) --> renvoie un bool�en 
            Cette fonction sert � charger les fichiers n�c�ssaires au traitement. Il est posible de charger plusieurs fichiers en un unique appel 
        - value([tableau associatif sous la forme 'nom de la variable' => 'valeur']) --> renvoie un bool�en 
            Cette fonction sert � remplacer une variable de template sous la forme {nom de la variable}. Il est possible d'indiquer plusieurs variables d'un coup 
        - bloc([nom du bloc], [variables dans ce bloc]) --> renvoie un bool�en 
            Cette fonction cr�e le bloc portant le nom indiqu�. Pour afficher plusieurs fois le m�me bloc de suite, faire plusieurs appels 
            S'il n'y a aucune variable � remplacer, le deuxi�me argument est facultatif. Les variables contenues dans un bloc sont indiqu�s dans les <span class="posthilit">templates</span> comme ceci :  
                nomdublocparent.nomdelavariable 
            S'il y a plusieurs blocs imbriqu�s (exemple avec 2 bloc, ca marche pareil pour plus de blocs) :  
                nomdublocparent.nomdusousbloc.nomdelavariable 
            Les variables d�finies sans cela sont globales, elles doivent �tre d�finies en dehors du bloc 
        - construire([surnomdufichier]) --> un texte 
            Le texte renvoy� est le contenu du template associ� au surnom indiqu� une fois modifi�.  
            Il faut renseigner les divers traitement avant l'appel � cette fonction et apr�s l'appel � la fonction setFiles. 
    */  
  
    private $classname = 'Template';  
  
    // variable that holds all the data we'll be substituting into the compiled <span class="posthilit">templates</span>.  
    // This will end up being a multi-dimensional array like this :  
    // $this->_tpldata[block.][iteration#][child.][iteration#][child2.][iteration#][variablename] == value  
    // if it's a root-level variable, it'll be like this :  
    // $this->_tpldata[.][0][varname] == value  
    private $_tpldata = array();  
  
    // Hash of filenames for each template handle.  
    private $files = array();  
  
    // Root template directory.  
    private $root = "";  
  
    // this will hash handle names to the compiled code for that handle.  
    private $compiled_code = array();  
  
    // This will hold the uncompiled code for that handle.  
    private $uncompiled_code = array();  
  
    //Constructor. Simply sets the root dir.  
    public function __construct($rootDir = './themes/html') {  
        $this->set_rootdir($rootDir);  
    }  
      
    //Erreur si on tente d'appeller une m�thode (fonction) inexistante  
   public function __call($nom, $arguments) {
		trigger_error('Template->'.$nom.'('.implode(', ',$arguments).') : Cette fonction n\'existe pas', E_USER_ERROR);
    }  
      
    public function __set($propriete, $valeur) {
		trigger_error('Template->'.$propriete.' : Cette propri�t� n\'existe pas et ne peut donc pas �tre fix�e � '.$valeur, E_USER_ERROR);
    }  
      
    public function __get($propriete) {
		trigger_error('Template->'.$propriete.' : Cette propri�t� n\'existe pas et ne peut donc pas �tre appell�e ', E_USER_ERROR);
    }  
  
    //Sets the template root directory for this Template object.  
    private function set_rootdir($dir) {  
        if(!is_dir($dir)) {  
            return false;  
        }  
          
        $this->root = $dir;  
        return true;  
    }  
  
    //Sets the template filenames for handles. $filename_array should be a hash of handle => filename pairs.  
    public function setFiles($filename_array) {  
        if (!is_array($filename_array)) {  
            return false;  
        }  
          
        reset($filename_array);  
		foreach($filename_array as $handle => $filename){
            $this->files[$handle] = $this->make_filename($filename);  
        }  
        return true;  
    }  
	
	public function setFile($name, $file) {
		$this->setFiles(Array($name => $file));
	}
  
    //Load the file for the handle, compile the file, and run the compiled code. This will print out the results of executing the template.  
    private function pparse($handle) {  
        if (!$this->loadfile($handle)) {  
			trigger_error('Template->pparse() : Impossible de charger le fichier de template afin de le traiter '.$handle, E_USER_ERROR);
        }  
  
        // actually compile the template now.  
        if (!isset($this->compiled_code[$handle]) || empty($this->compiled_code[$handle])) {  
            // Actually compile the code now.  
            $this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle], true, 'compilationVariable');  
        }  
  
        // Run the compiled code.  
		$compilationVariable = '';
        eval($this->compiled_code[$handle]);  
        return $compilationVariable;  
    }  
  
  
    //Inserts the uncompiled code for $handle as the value of $varname in the root-level. This can be used to effectively include a template in the middle of another  
    //template. Note that all desired assignments to the variables in $handle should be done BEFORE calling this function.  
    private function assign_var_from_handle($varname, $handle) {  
        if (!$this->loadfile($handle)) {  
			trigger_error('Template->assign_var_from_handle() : Impossible de charger le fichier de template afin de le traiter '.$handle, E_USER_ERROR);
        }  
  
        // Compile it, with the "no echo statements" option on.  
        $_str = '';  
        $code = $this->compile($this->uncompiled_code[$handle], true, '_str');  
  
        // evaluate the variable assignment.  
        eval($code);  
        // assign the value of the generated variable to the given varname.  
        $this->assign_var($varname, $_str);  
  
        return true;  
    }  
  
    //Block-level variable assignment. Adds a new block iteration with the given variable assignments. Note that this should only be called once per block iteration.  
    public function bloc($blockname, $vararray = Array()) {  
        if (strstr($blockname, '.')) {  
            // Nested block.  
            $blocks = explode('.', $blockname);  
            $blockcount = sizeof($blocks) - 1;  
            $str = '$this->_tpldata';  
            for ($i = 0; $i < $blockcount; $i++) {  
                $str .= '[\'' . $blocks[$i] . '.\']';  
                eval('$lastiteration = sizeof(' . $str . ') - 1;');  
                $str .= '[' . $lastiteration . ']';  
            }  
            // Now we add the block that we're actually assigning to. We're adding a new iteration to this block with the given variable assignments.  
            $str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';  
  
            // Now we evaluate this assignment we've built up.  
            eval($str);  
        } else {  
            // Top-level block. Add a new iteration to this block with the variable assignments we were given.  
            $this->_tpldata[$blockname . '.'][] = $vararray;  
        }  
  
        return true;  
    }  
  
    //Root-level variable assignment. Adds to current assignments, overriding any existing variable assignment with the same name.  
    public function values($vararray) {  
		reset($vararray);  
		foreach($vararray as $key => $val){
            $this->_tpldata['.'][0][$key] = $val;  
        }  
  
        return true;  
    }  
	
	public function value($name, $content) {
		$this->values(Array($name => $content));
	}
  
    //Root-level variable assignment. Adds to current assignments, overriding any existing variable assignment with the same name.  
    private function assign_var($varname, $varval) {  
        $this->_tpldata['.'][0][$varname] = $varval;  
  
        return true;  
    }  
  
  
    //Generates a full path+filename for the given filename, which can either be an absolute name, or a name relative to the rootdir for this Template object.  
    private function make_filename($filename) {  
        // Check if it's an absolute or relative path.  
        if (substr($filename, 0, 1) != '/') {  
            $filename = ($rp_filename = realpath($this->root . '/' . $filename)) ? $rp_filename : $filename;  
        }  
  
        if (!file_exists($filename)) { 
			trigger_error('Template->make_filename() : Le fichier '.$filename.' n\'existe pas', E_USER_ERROR); 
        }  
  
        return $filename;  
    }  
  
    //If not already done, load the file for the given handle and populate the uncompiled_code[] hash with its code. Do not compile.  
    private function loadfile($handle) {  
        // If the file for this handle is already loaded and compiled, do nothing.  
        if (isset($this->uncompiled_code[$handle]) && !empty($this->uncompiled_code[$handle])) {  
            return true;  
        }  
  
        // If we don't have a file assigned to this handle, die.  
        if (!isset($this->files[$handle])) {
			trigger_error('Template->loadfile() : Aucun fichier n\'a �t� indiqu� pour le traiter '.$handle, E_USER_ERROR);
        }  
  
        $filename = $this->files[$handle];  
  
        $str = implode("", @file($filename));  
        if (empty($str)) {
            trigger_error('Template->loadfile() : Le fichier '.$filename.' pour le traitement '.$handle.' est vide', E_USER_ERROR);  
        }  
  
        $this->uncompiled_code[$handle] = $str;  
  
        return true;  
    }  
  
    //Compiles the given string of code, and returns the result in a string. If "do_not_echo" is true, the returned code will not be directly  
    //executable, but can be used as part of a variable assignment for use in assign_code_from_handle().  
    private function compile($code, $do_not_echo = false, $retvar = '') {  
        // replace \ with \\ and then ' with \'.  
        $code = str_replace('\\', '\\\\', $code);  
        $code = str_replace('\'', '\\\'', $code);  
  
        // change template varrefs into PHP varrefs  
  
        // This one will handle varrefs WITH namespaces  
        $varrefs = array();  
        preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);  
        $varcount = sizeof($varrefs[1]);  
        for ($i = 0; $i < $varcount; $i++) {  
            $namespace = $varrefs[1][$i];  
            $varname = $varrefs[3][$i];  
            $new = $this->generate_block_varref($namespace, $varname);  
  
            $code = str_replace($varrefs[0][$i], $new, $code);  
        }  
  
        // This will handle the remaining root-level varrefs  
        $code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->_tpldata[\'.\'][0][\'\1\']) ) ? $this->_tpldata[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);  
  
        // Break it up into lines.  
        $code_lines = explode("\n", $code);  
  
        $block_nesting_level = 0;  
        $block_names = array();  
        $block_names[0] = ".";  
  
        // Second: prepend echo ', append ' . "\n"; to each line.  
        $line_count = sizeof($code_lines);  
        for ($i = 0; $i < $line_count; $i++) {  
            $code_lines[$i] = chop($code_lines[$i]);  
            if (preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m)) {  
                $n[0] = $m[0];  
                $n[1] = $m[1];  
  
                // Added: dougk_ff7-Keeps <span class="posthilit">templates</span> from bombing if BEGIN is on the same line as END.. I think. <img src="./images/smilies/icon_smile.gif" alt=":)" title="Smile" />  
                if(preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n)) {  
                    $block_nesting_level++;  
                    $block_names[$block_nesting_level] = $m[1];  
                    if($block_nesting_level < 2) {  
                        // Block is not nested.  
                        $code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->_tpldata[\'' . $n[1] . '.\']) ) ?  sizeof($this->_tpldata[\'' . $n[1] . '.\']) : 0;';  
                        $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';  
                        $code_lines[$i] .= "\n" . '{';  
                    } else {  
                        // This block is nested.  
  
                        // Generate a namespace string for this block.  
                        $namespace = implode('.', $block_names);  
                        // strip leading period from root level..  
                        $namespace = substr($namespace, 2);  
                        // Get a reference to the data array for this block that depends on the  
                        // current indices of all parent blocks.  
                        $varref = $this->generate_block_data_ref($namespace, false);  
                        // Create the for loop code to iterate over this block.  
                        $code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';  
                        $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';  
                        $code_lines[$i] .= "\n" . '{';  
                    }  
  
                    // We have the end of a block.  
                    unset($block_names[$block_nesting_level]);  
                    $block_nesting_level--;  
                    $code_lines[$i] .= '} // END ' . $n[1];  
                    $m[0] = $n[0];  
                    $m[1] = $n[1];  
                } else {  
                    // We have the start of a block.  
                    $block_nesting_level++;  
                    $block_names[$block_nesting_level] = $m[1];  
                    if ($block_nesting_level < 2) {  
                        // Block is not nested.  
                        $code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->_tpldata[\'' . $m[1] . '.\']) ) ? sizeof($this->_tpldata[\'' . $m[1] . '.\']) : 0;';  
                        $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';  
                        $code_lines[$i] .= "\n" . '{';  
                    } else {  
                        // This block is nested.  
  
                        // Generate a namespace string for this block.  
                        $namespace = implode('.', $block_names);  
                        // strip leading period from root level..  
                        $namespace = substr($namespace, 2);  
                        // Get a reference to the data array for this block that depends on the  
                        // current indices of all parent blocks.  
                        $varref = $this->generate_block_data_ref($namespace, false);  
                        // Create the for loop code to iterate over this block.  
                        $code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';  
                        $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';  
                        $code_lines[$i] .= "\n" . '{';  
                    }  
                }  
            } else if (preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m)) {  
                // We have the end of a block.  
                unset($block_names[$block_nesting_level]);  
                $block_nesting_level--;  
                $code_lines[$i] = '} // END ' . $m[1];  
            } else {  
              
              
                // We have an ordinary line of code.  
                if (!$do_not_echo) {  
                    $code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';  
                } else {  
                    $code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";';   
                }  
            }  
        }   
          
        // Bring it back into a single string of lines of code.  
        $code = implode("\n", $code_lines);  
        return $code;  
    }  
  
    //Generates a reference to the given variable inside the given (possibly nested)  block namespace. This is a string of the form :  
    //' . $this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '  
    //It's ready to be inserted into an "echo" line in one of the <span class="posthilit">templates</span>. NOTE : expects a trailing "." on the namespace.  
    private function generate_block_varref($namespace, $varname) {  
        // Strip the trailing period.  
        $namespace = substr($namespace, 0, strlen($namespace) - 1);  
  
        // Get a reference to the data block for this namespace.  
        $varref = $this->generate_block_data_ref($namespace, true);  
        // Prepend the necessary code to stick this in an echo line.  
  
        // Append the variable reference.  
        $varref .= '[\'' . $varname . '\']';  
        $varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';  
  
        return $varref;  
    }  
  
    //Generates a reference to the array of data values for the given (possibly nested) block namespace. This is a string of the form :  
    //$this->_tpldata['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']  
    //If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above. NOTE: does not expect a trailing "." on the blockname.  
    private function generate_block_data_ref($blockname, $include_last_iterator) {  
        // Get an array of the blocks involved.  
        $blocks = explode('.', $blockname);  
        $blockcount = sizeof($blocks) - 1;  
        $varref = '$this->_tpldata';  
        // Build up the string with everything but the last child.  
        for ($i = 0; $i < $blockcount; $i++) {  
            $varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';  
        }  
        // Add the block reference for the last child.  
        $varref .= '[\'' . $blocks[$blockcount] . '.\']';  
        // Add the iterator for the last child if requried.  
        if ($include_last_iterator) {  
            $varref .= '[$_' . $blocks[$blockcount] . '_i]';  
        }  
  
        return $varref;  
    }  
      
    public function construire($nom) { //sert � compiler le template sans l'�crire.  
       ob_start(); //on d�marre la temporisation de sortie  
        return $this->pparse($nom); //on compile le template, qui est mis en tampon  
        $contenu = ob_get_contents(); //on r�cup�re le texte  
       ob_end_clean(); //on arr�te la temporisation et on vide le tampon  
        return $contenu;
    } 

	 
}  
  
?>
