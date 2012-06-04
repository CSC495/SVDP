/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
import java.io.File;
import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.chartthemes.simple.AegeanSettingsFactory;
import net.sf.jasperreports.chartthemes.simple.EyeCandySixtiesSettingsFactory;
import net.sf.jasperreports.chartthemes.simple.SimpleSettingsFactory;
import net.sf.jasperreports.chartthemes.simple.XmlChartTheme;
import net.sf.jasperreports.engine.JREmptyDataSource;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExporterParameter;
import net.sf.jasperreports.engine.JasperExportManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperPrintManager;
import net.sf.jasperreports.engine.data.JRCsvDataSource;
import net.sf.jasperreports.engine.export.JExcelApiExporter;
import net.sf.jasperreports.engine.export.JRCsvExporter;
import net.sf.jasperreports.engine.export.JRRtfExporter;
import net.sf.jasperreports.engine.export.JRXhtmlExporter;
import net.sf.jasperreports.engine.export.JRXlsExporter;
import net.sf.jasperreports.engine.export.JRXlsExporterParameter;
import net.sf.jasperreports.engine.export.oasis.JROdsExporter;
import net.sf.jasperreports.engine.export.oasis.JROdtExporter;
import net.sf.jasperreports.engine.export.ooxml.JRDocxExporter;
import net.sf.jasperreports.engine.export.ooxml.JRPptxExporter;
import net.sf.jasperreports.engine.export.ooxml.JRXlsxExporter;
import net.sf.jasperreports.engine.util.AbstractSampleApp;
import net.sf.jasperreports.engine.util.JRLoader;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: ChartThemesApp.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class ChartThemesApp extends AbstractSampleApp
{


	/**
	 *
	 */
	public static void main(String[] args)
	{
		main(new ChartThemesApp(), args);
	}
	
	
	/**
	 *
	 */
	public void test() throws JRException
	{
		fill();
		pdf();
		xmlEmbed();
		xml();
		html();
		rtf();
		xls();
		jxl();
		csv();
		odt();
		ods();
		docx();
		xlsx();
		pptx();
		xhtml();
	}


	/**
	 *
	 */
	public void themes() throws JRException
	{
		long start = System.currentTimeMillis();
		XmlChartTheme.saveSettings(
			SimpleSettingsFactory.createChartThemeSettings(), 
			new File("src/net/sf/jasperreports/chartthemes/simple/simple.jrctx")
			);
		XmlChartTheme.saveSettings(
			EyeCandySixtiesSettingsFactory.createChartThemeSettings(), 
			new File("src/net/sf/jasperreports/chartthemes/simple/eye.candy.sixties.jrctx")
			);
		XmlChartTheme.saveSettings(
			AegeanSettingsFactory.createChartThemeSettings(), 
			new File("src/net/sf/jasperreports/chartthemes/simple/aegean.jrctx")
			);
		System.err.println("Theme saving time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void fill() throws JRException
	{
		long start = System.currentTimeMillis();
		Map parameters = new HashMap();
		
		putDataSources(parameters);
		
		JasperFillManager.fillReportToFile("build/reports/AllChartsReport.jasper", parameters, new JREmptyDataSource());
		System.err.println("Filling time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void print() throws JRException
	{
		long start = System.currentTimeMillis();
		JasperPrintManager.printReport("build/reports/AllChartsReport.jrprint", true);
		System.err.println("Printing time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void pdf() throws JRException
	{
		long start = System.currentTimeMillis();
		JasperExportManager.exportReportToPdfFile("build/reports/AllChartsReport.jrprint");
		System.err.println("PDF creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void xml() throws JRException
	{
		long start = System.currentTimeMillis();
		JasperExportManager.exportReportToXmlFile("build/reports/AllChartsReport.jrprint", false);
		System.err.println("XML creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void xmlEmbed() throws JRException
	{
		long start = System.currentTimeMillis();
		JasperExportManager.exportReportToXmlFile("build/reports/AllChartsReport.jrprint", true);
		System.err.println("XML creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void html() throws JRException
	{
		long start = System.currentTimeMillis();
		JasperExportManager.exportReportToHtmlFile("build/reports/AllChartsReport.jrprint");
		System.err.println("HTML creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void rtf() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".rtf");
		
		JRRtfExporter exporter = new JRRtfExporter();
		
		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		
		exporter.exportReport();

		System.err.println("RTF creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void xls() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");
		Map dateFormats = new HashMap();
		dateFormats.put("EEE, MMM d, yyyy", "ddd, mmm d, yyyy");
		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);
		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".xls");
		
		JRXlsExporter exporter = new JRXlsExporter();
		
		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		exporter.setParameter(JRXlsExporterParameter.IS_ONE_PAGE_PER_SHEET, Boolean.TRUE);
		exporter.setParameter(JRXlsExporterParameter.IS_DETECT_CELL_TYPE, Boolean.TRUE);
		exporter.setParameter(JRXlsExporterParameter.FORMAT_PATTERNS_MAP, dateFormats);
		
		exporter.exportReport();

		System.err.println("XLS creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void jxl() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".jxl.xls");

		JExcelApiExporter exporter = new JExcelApiExporter();

		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		exporter.setParameter(JRXlsExporterParameter.IS_ONE_PAGE_PER_SHEET, Boolean.TRUE);

		exporter.exportReport();

		System.err.println("XLS creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void csv() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".csv");
		
		JRCsvExporter exporter = new JRCsvExporter();
		
		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		
		exporter.exportReport();

		System.err.println("CSV creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void odt() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".odt");

		JROdtExporter exporter = new JROdtExporter();

		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());

		exporter.exportReport();

		System.err.println("ODT creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void ods() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".ods");

		JROdsExporter exporter = new JROdsExporter();

		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		exporter.setParameter(JRXlsExporterParameter.IS_ONE_PAGE_PER_SHEET, Boolean.TRUE);

		exporter.exportReport();

		System.err.println("ODS creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void docx() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".docx");

		JRDocxExporter exporter = new JRDocxExporter();

		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());

		exporter.exportReport();

		System.err.println("DOCX creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void xlsx() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");
		Map dateFormats = new HashMap();
		dateFormats.put("EEE, MMM d, yyyy", "ddd, mmm d, yyyy");
		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);
		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".xlsx");
		
		JRXlsxExporter exporter = new JRXlsxExporter();
		
		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());
		exporter.setParameter(JRXlsExporterParameter.IS_ONE_PAGE_PER_SHEET, Boolean.TRUE);
		exporter.setParameter(JRXlsExporterParameter.IS_DETECT_CELL_TYPE, Boolean.TRUE);
		exporter.setParameter(JRXlsExporterParameter.FORMAT_PATTERNS_MAP, dateFormats);
		
		exporter.exportReport();

		System.err.println("XLSX creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void pptx() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".pptx");
		
		JRPptxExporter exporter = new JRPptxExporter();
		
		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());

		exporter.exportReport();

		System.err.println("PPTX creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public void xhtml() throws JRException
	{
		long start = System.currentTimeMillis();
		File sourceFile = new File("build/reports/AllChartsReport.jrprint");

		JasperPrint jasperPrint = (JasperPrint)JRLoader.loadObject(sourceFile);

		File destFile = new File(sourceFile.getParent(), jasperPrint.getName() + ".x.html");

		JRXhtmlExporter exporter = new JRXhtmlExporter();

		exporter.setParameter(JRExporterParameter.JASPER_PRINT, jasperPrint);
		exporter.setParameter(JRExporterParameter.OUTPUT_FILE_NAME, destFile.toString());

		exporter.exportReport();

		System.err.println("XHTML creation time : " + (System.currentTimeMillis() - start));
	}
	
	
	/**
	 *
	 */
	public static final void putDataSources(Map parameters) throws JRException
	{
		try
		{
			JRCsvDataSource cds1 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds1.setRecordDelimiter("\r\n");
			cds1.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource1", cds1);
			
			JRCsvDataSource cds2 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds2.setRecordDelimiter("\r\n");
			cds2.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource2", cds2);
			
			JRCsvDataSource cds3 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds3.setRecordDelimiter("\r\n");
			cds3.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource3", cds3);
			
			JRCsvDataSource cds4 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds4.setRecordDelimiter("\r\n");
			cds4.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource4", cds4);
			
			JRCsvDataSource cds5 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds5.setRecordDelimiter("\r\n");
			cds5.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource5", cds5);
			
			JRCsvDataSource cds6 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds6.setRecordDelimiter("\r\n");
			cds6.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource6", cds6);
			
			JRCsvDataSource cds7 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/categoryDatasource.csv"), "UTF-8");
			cds7.setRecordDelimiter("\r\n");
			cds7.setUseFirstRowAsHeader(true);
			parameters.put("categoryDatasource7", cds7);
			
			JRCsvDataSource pds1 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/pieDatasource.csv"), "UTF-8");
			pds1.setRecordDelimiter("\r\n");
			pds1.setUseFirstRowAsHeader(true);
			parameters.put("pieDatasource1", pds1);
			
			JRCsvDataSource pds2 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/pieDatasource.csv"), "UTF-8");
			pds2.setRecordDelimiter("\r\n");
			pds2.setUseFirstRowAsHeader(true);
			parameters.put("pieDatasource2", pds2);
			
			JRCsvDataSource tpds1 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/timePeriodDatasource.csv"), "UTF-8");
			tpds1.setRecordDelimiter("\r\n");
			tpds1.setUseFirstRowAsHeader(true);
			parameters.put("timePeriodDatasource1", tpds1);
			
			JRCsvDataSource tsds1 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/timeSeriesDatasource.csv"), "UTF-8");
			tsds1.setRecordDelimiter("\r\n");
			tsds1.setUseFirstRowAsHeader(true);
			tsds1.setDateFormat(new SimpleDateFormat("yyyy-MM-dd hh:mm:ss"));
			parameters.put("timeSeriesDatasource1", tsds1);
			
			JRCsvDataSource tsds2 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/timeSeriesDatasource.csv"), "UTF-8");
			tsds2.setRecordDelimiter("\r\n");
			tsds2.setUseFirstRowAsHeader(true);
			tsds2.setDateFormat(new SimpleDateFormat("yyyy-MM-dd hh:mm:ss"));
			parameters.put("timeSeriesDatasource2", tsds2);
			
			JRCsvDataSource tsds3 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/timeSeriesDatasource.csv"), "UTF-8");
			tsds3.setRecordDelimiter("\r\n");
			tsds3.setUseFirstRowAsHeader(true);
			tsds3.setDateFormat(new SimpleDateFormat("yyyy-MM-dd hh:mm:ss"));
			parameters.put("timeSeriesDatasource3", tsds3);
			
			JRCsvDataSource xyds1 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/xyDatasource.csv"), "UTF-8");
			xyds1.setRecordDelimiter("\r\n");
			xyds1.setUseFirstRowAsHeader(true);
			parameters.put("xyDatasource1", xyds1);
			
			JRCsvDataSource xyds2 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/xyDatasource.csv"), "UTF-8");
			xyds2.setRecordDelimiter("\r\n");
			xyds2.setUseFirstRowAsHeader(true);
			parameters.put("xyDatasource2", xyds2);
			
			JRCsvDataSource xyds3 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/xyDatasource.csv"), "UTF-8");
			xyds3.setRecordDelimiter("\r\n");
			xyds3.setUseFirstRowAsHeader(true);
			parameters.put("xyDatasource3", xyds3);
			
			JRCsvDataSource xyds4 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/xyDatasource.csv"), "UTF-8");
			xyds4.setRecordDelimiter("\r\n");
			xyds4.setUseFirstRowAsHeader(true);
			parameters.put("xyDatasource4", xyds4);
			
			JRCsvDataSource xyds5 = new JRCsvDataSource(JRLoader.getLocationInputStream("data/xyDatasource.csv"), "UTF-8");
			xyds5.setRecordDelimiter("\r\n");
			xyds5.setUseFirstRowAsHeader(true);
			parameters.put("xyDatasource5", xyds5);
		}
		catch (UnsupportedEncodingException e)
		{
			throw new JRException(e);
		}
	}

}
