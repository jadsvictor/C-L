/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package javaapplication3;
import javax.swing.*;
/**
 *
 * @author jads
 */
public class JavaApplication3 {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
int opcao;
        opcao = Integer.parseInt(JOptionPane.showInputDialog("Informe a opção desejada:"
                + "1 - cadastrar"
                + "2 - exlcuir"));
        
        if (opcao == 1)
            JOptionPane.showMessageDialog(null, "certo");
    }
}
