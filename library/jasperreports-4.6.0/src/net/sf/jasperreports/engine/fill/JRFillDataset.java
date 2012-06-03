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
package net.sf.jasperreports.engine.fill;

import java.sql.Connection;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.ResourceBundle;
import java.util.Set;
import java.util.TimeZone;
import java.util.UUID;

import net.sf.jasperreports.data.cache.CachedDataset;
import net.sf.jasperreports.data.cache.DataCacheHandler;
import net.sf.jasperreports.data.cache.DataRecorder;
import net.sf.jasperreports.data.cache.DataSnapshot;
import net.sf.jasperreports.data.cache.DataSnapshotException;
import net.sf.jasperreports.data.cache.DatasetRecorder;
import net.sf.jasperreports.engine.DatasetFilter;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.EvaluationType;
import net.sf.jasperreports.engine.JRAbstractScriptlet;
import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRDefaultScriptlet;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRGroup;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JRPropertiesHolder;
import net.sf.jasperreports.engine.JRPropertiesMap;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRQuery;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRScriptlet;
import net.sf.jasperreports.engine.JRSortField;
import net.sf.jasperreports.engine.JRVariable;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.ParameterContributor;
import net.sf.jasperreports.engine.ParameterContributorContext;
import net.sf.jasperreports.engine.ParameterContributorFactory;
import net.sf.jasperreports.engine.data.IndexedDataSource;
import net.sf.jasperreports.engine.design.JRDesignVariable;
import net.sf.jasperreports.engine.query.JRQueryExecuter;
import net.sf.jasperreports.engine.query.QueryExecuterFactory;
import net.sf.jasperreports.engine.scriptlets.ScriptletFactory;
import net.sf.jasperreports.engine.scriptlets.ScriptletFactoryContext;
import net.sf.jasperreports.engine.type.CalculationEnum;
import net.sf.jasperreports.engine.type.IncrementTypeEnum;
import net.sf.jasperreports.engine.type.ResetTypeEnum;
import net.sf.jasperreports.engine.type.WhenResourceMissingTypeEnum;
import net.sf.jasperreports.engine.util.DigestUtils;
import net.sf.jasperreports.engine.util.JRQueryExecuterUtils;
import net.sf.jasperreports.engine.util.JRResourcesUtil;
import net.sf.jasperreports.engine.util.MD5Digest;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRFillDataset.java 5340 2012-05-04 10:41:48Z lucianc $
 */
public class JRFillDataset implements JRDataset, DatasetFillContext
{
	
	private static final Log log = LogFactory.getLog(JRFillDataset.class);
	
	/**
	 * The filler that created this object.
	 */
	private final JRBaseFiller filler;
	
	/**
	 *
	 */
	private JasperReportsContext jasperReportsContext;
	
	/**
	 * The template dataset.
	 */
	private final JRDataset parent;
	
	/**
	 * Whether this is the main dataset of the report.
	 */
	private final boolean isMain;
	
	/**
	 * The dataset query.
	 */
	protected JRQuery query;
	
	private boolean useDatasourceParamValue;
	private boolean useConnectionParamValue;
	
	/**
	 * The dataset parameters.
	 */
	protected JRFillParameter[] parameters;

	/**
	 * The dataset parameters indexed by name.
	 */
	protected Map<String,JRFillParameter> parametersMap;

	/**
	 * The dataset fields.
	 */
	protected JRFillField[] fields;
	
	/**
	 * The dataset fields indexed by name.
	 */
	protected Map<String,JRFillField> fieldsMap;
	
	/**
	 * The dataset variables.
	 */
	protected JRFillVariable[] variables;
	
	/**
	 * The dataset variables indexed by name.
	 */
	protected Map<String,JRFillVariable> variablesMap;
	
	/**
	 * Set of {@link VariableCalculationReq VariableCalculationReq} objects.
	 */
	protected Set<VariableCalculationReq> variableCalculationReqs;

	/**
	 * The element datasets.
	 */
	protected JRFillElementDataset[] elementDatasets;
	
	/**
	 * Used to save the original element datasets when
	 * {@link #filterElementDatasets(JRFillElementDataset) filterElementDatasets} is called.
	 */
	protected JRFillElementDataset[] origElementDatasets;

	/**
	 * The dataset groups.
	 */
	protected JRFillGroup[] groups;

	/**
	 * The resource bundle base name.
	 */
	protected String resourceBundleBaseName;
	
	/**
	 * The resource missing handle type.
	 */
	protected WhenResourceMissingTypeEnum whenResourceMissingType;
	
	/**
	 * The scriptlet class name.
	 */
	protected String scriptletClassName;

	/**
	 * The data source. 
	 */
	protected JRDataSource dataSource;
	
	/**
	 * The {@link Locale Locale} to be used by the dataset.
	 */
	protected Locale locale;
	
	/**
	 * The loaded resource bundle.
	 */
	protected ResourceBundle resourceBundle;

	/**
	 * The {@link TimeZone TimeZone} to be used by the dataset.
	 */
	protected TimeZone timeZone;
	
	/**
	 * The cursor used when iterating the data source.
	 */
	protected int reportCount;

	/**
	 * The calculator used by the dataset.
	 */
	protected JRCalculator calculator;

	/**
	 * The scriptlets used by the dataset.
	 */
	protected List<JRAbstractScriptlet> scriptlets;

	/**
	 *
	 */
	protected JRAbstractScriptlet delegateScriptlet = new JRFillDatasetScriptlet(this);

	/**
	 * The value of the {@link JRParameter#REPORT_MAX_COUNT max count} parameter.
	 */
	protected Integer reportMaxCount;

	private JRQueryExecuter queryExecuter;
	private List<ParameterContributor> parameterContributors;
	
	protected DatasetFilter filter;

	protected FillDatasetPosition fillPosition;
	protected DatasetRecorder dataRecorder;
	
	private Map<Integer, CacheRecordIndexCallback> cacheRecordIndexCallbacks;
	private boolean cacheSkipped;
	private CachedDataset cachedDataset;
	private boolean sortedDataSource;
	
	private boolean ended;
	private int cacheRecordCount;
	private int previousCacheRecordIndex;
	private int currentCacheRecordIndex;
	
	/**
	 * Creates a fill dataset object.
	 * @param filler the filler
	 * @param dataset the template dataset
	 * @param factory the fill object factory
	 */
	public JRFillDataset(JRBaseFiller filler, JRDataset dataset, JRFillObjectFactory factory)
	{
		factory.put(dataset, this);
		
		this.filler = filler;
		this.parent = dataset;
		this.isMain = dataset.isMainDataset();
		
		scriptletClassName = dataset.getScriptletClass();
		resourceBundleBaseName = dataset.getResourceBundle();
		whenResourceMissingType = dataset.getWhenResourceMissingTypeValue();
		
		query = dataset.getQuery();
		
		setParameters(dataset, factory);

		setFields(dataset, factory);
		
		setVariables(dataset, factory);
		
		setGroups(dataset, factory);
	}

	
	private void setParameters(JRDataset dataset, JRFillObjectFactory factory)
	{
		JRParameter[] jrParameters = dataset.getParameters();
		if (jrParameters != null && jrParameters.length > 0)
		{
			parameters = new JRFillParameter[jrParameters.length];
			parametersMap = new HashMap<String,JRFillParameter>();
			for (int i = 0; i < parameters.length; i++)
			{
				parameters[i] = factory.getParameter(jrParameters[i]);
				parametersMap.put(parameters[i].getName(), parameters[i]);
			}
		}
	}


	private void setGroups(JRDataset dataset, JRFillObjectFactory factory)
	{
		JRGroup[] jrGroups = dataset.getGroups();
		if (jrGroups != null && jrGroups.length > 0)
		{
			groups = new JRFillGroup[jrGroups.length];
			for (int i = 0; i < groups.length; i++)
			{
				groups[i] = factory.getGroup(jrGroups[i]);
			}
		}
	}


	private void setVariables(JRDataset dataset, JRFillObjectFactory factory)
	{
		JRVariable[] jrVariables = dataset.getVariables();
		if (jrVariables != null && jrVariables.length > 0)
		{
			List<JRFillVariable> variableList = new ArrayList<JRFillVariable>(jrVariables.length * 3);

			variablesMap = new HashMap<String,JRFillVariable>();
			for (int i = 0; i < jrVariables.length; i++)
			{
				addVariable(jrVariables[i], variableList, factory);
			}

			setVariables(variableList);
		}
	}
	
	
	private JRFillVariable addVariable(JRVariable parentVariable, List<JRFillVariable> variableList, JRFillObjectFactory factory)
	{
		JRFillVariable variable = factory.getVariable(parentVariable);

		CalculationEnum calculation = variable.getCalculationValue();
		switch (calculation)
		{
			case AVERAGE:
			case VARIANCE:
			{
				JRVariable countVar = createHelperVariable(parentVariable, "_COUNT", CalculationEnum.COUNT);
				JRFillVariable fillCountVar = addVariable(countVar, variableList, factory);
				variable.setHelperVariable(fillCountVar, JRCalculable.HELPER_COUNT);

				JRVariable sumVar = createHelperVariable(parentVariable, "_SUM", CalculationEnum.SUM);
				JRFillVariable fillSumVar = addVariable(sumVar, variableList, factory);
				variable.setHelperVariable(fillSumVar, JRCalculable.HELPER_SUM);

				break;
			}
			case STANDARD_DEVIATION:
			{
				JRVariable varianceVar = createHelperVariable(parentVariable, "_VARIANCE", CalculationEnum.VARIANCE);
				JRFillVariable fillVarianceVar = addVariable(varianceVar, variableList, factory);
				variable.setHelperVariable(fillVarianceVar, JRCalculable.HELPER_VARIANCE);

				break;
			}
			case DISTINCT_COUNT:
			{
				JRVariable countVar = createDistinctCountHelperVariable(parentVariable);
				JRFillVariable fillCountVar = addVariable(countVar, variableList, factory);
				variable.setHelperVariable(fillCountVar, JRCalculable.HELPER_COUNT);

				break;
			}
		}

		variableList.add(variable);
		return variable;
	}

	private JRVariable createHelperVariable(JRVariable variable, String nameSuffix, CalculationEnum calculation)
	{
		JRDesignVariable helper = new JRDesignVariable();
		helper.setName(variable.getName() + nameSuffix);
		helper.setValueClassName(variable.getValueClassName());
		helper.setIncrementerFactoryClassName(variable.getIncrementerFactoryClassName());
		helper.setResetType(variable.getResetTypeValue());
		helper.setResetGroup(variable.getResetGroup());
		helper.setIncrementType(variable.getIncrementTypeValue());
		helper.setIncrementGroup(variable.getIncrementGroup());
		helper.setCalculation(calculation);
		helper.setSystemDefined(true);
		helper.setExpression(variable.getExpression());

		return helper;
	}

	private JRVariable createDistinctCountHelperVariable(JRVariable variable)
	{
		JRDesignVariable helper = new JRDesignVariable();
		helper.setName(variable.getName() + "_DISTINCT_COUNT");
		helper.setValueClassName(variable.getValueClassName());
		helper.setIncrementerFactoryClassName(JRDistinctCountIncrementerFactory.class.getName());
		helper.setResetType(ResetTypeEnum.REPORT);

		if (variable.getIncrementTypeValue() != IncrementTypeEnum.NONE)
		{
			helper.setResetType(ResetTypeEnum.getByValue(variable.getIncrementTypeValue().getValue()));
		}
		helper.setResetGroup(variable.getIncrementGroup());
		helper.setCalculation(CalculationEnum.NOTHING);
		helper.setSystemDefined(true);
		helper.setExpression(variable.getExpression());
		
		return helper;
	}

	private void setVariables(List<JRFillVariable> variableList)
	{
		variables = new JRFillVariable[variableList.size()];
		variables = variableList.toArray(variables);

		for (int i = 0; i < variables.length; i++)
		{
			variablesMap.put(variables[i].getName(), variables[i]);
		}
	}


	private void setFields(JRDataset dataset, JRFillObjectFactory factory)
	{
		JRField[] jrFields = dataset.getFields();
		if (jrFields != null && jrFields.length > 0)
		{
			fields = new JRFillField[jrFields.length];
			fieldsMap = new HashMap<String,JRFillField>();
			for (int i = 0; i < fields.length; i++)
			{
				fields[i] = factory.getField(jrFields[i]);
				fieldsMap.put(fields[i].getName(), fields[i]);
			}
		}
	}


	/**
	 * Creates the calculator
	 * @param jasperReport the report
	 * @throws JRException
	 */
	public void createCalculator(JasperReport jasperReport) throws JRException
	{
		setCalculator(createCalculator(getJasperReportsContext(), jasperReport, this));
	}

	protected void setCalculator(JRCalculator calculator)
	{
		this.calculator = calculator;
	}

	protected static JRCalculator createCalculator(JasperReportsContext jasperReportsContext, JasperReport jasperReport, JRDataset dataset) throws JRException
	{
		JREvaluator evaluator = JasperCompileManager.getInstance(jasperReportsContext).getEvaluator(jasperReport, dataset);
		return new JRCalculator(evaluator);
	}


	/**
	 * Initializes the calculator.
	 * 
	 * @throws JRException
	 */
	public void initCalculator() throws JRException
	{
		calculator.init(this);
	}


	/**
	 * Inherits properties from the report.
	 */
	protected void inheritFromMain()
	{
		if (resourceBundleBaseName == null && !isMain)
		{
			resourceBundleBaseName = filler.mainDataset.resourceBundleBaseName;
			whenResourceMissingType = filler.mainDataset.whenResourceMissingType;
		}
	}
	
	
	/**
	 * Creates the scriptlets.
	 * 
	 * @return the scriptlets list
	 * @throws JRException
	 */
	protected List<JRAbstractScriptlet> createScriptlets(Map<String,Object> parameterValues) throws JRException
	{
		ScriptletFactoryContext context = new ScriptletFactoryContext(getJasperReportsContext(), this, parameterValues);
		
		scriptlets = new ArrayList<JRAbstractScriptlet>();
		
		List<ScriptletFactory> factories = getJasperReportsContext().getExtensions(ScriptletFactory.class);
		for (Iterator<ScriptletFactory> it = factories.iterator(); it.hasNext();)
		{
			ScriptletFactory factory = it.next();
			List<JRAbstractScriptlet> tmpScriptlets = factory.getScriplets(context);
			if (tmpScriptlets != null)
			{
				scriptlets.addAll(tmpScriptlets);
			}
		}
		
		if (scriptlets.size() == 0)
		{
			scriptlets.add(0, new JRDefaultScriptlet());
		}

		return scriptlets;
	}


	/**
	 * Initializes the element datasets.
	 * 
	 * @param factory the fill object factory used by the filler
	 */
	protected void initElementDatasets(JRFillObjectFactory factory)
	{
		elementDatasets = factory.getElementDatasets(this);
	}


	/**
	 * Filters the element datasets, leaving only one.
	 * <p>
	 * This method is used when a dataset is instantiated by a chart or crosstab.
	 * 
	 * @param elementDataset the element dataset that should remain
	 */
	protected void filterElementDatasets(JRFillElementDataset elementDataset)
	{
		origElementDatasets = elementDatasets;
		elementDatasets = new JRFillElementDataset[]{elementDataset};
	}
	
	
	/**
	 * Restores the original element datasets.
	 * <p>
	 * This method should be called after {@link #filterElementDatasets(JRFillElementDataset) filterElementDatasets}.
	 */
	protected void restoreElementDatasets()
	{
		if (origElementDatasets != null)
		{
			elementDatasets = origElementDatasets;
			origElementDatasets = null;
		}
	}
	

	/**
	 * Loads the resource bundle corresponding to the resource bundle base name and locale.
	 */
	protected ResourceBundle loadResourceBundle()
	{
		ResourceBundle loadedBundle;
		if (resourceBundleBaseName == null)
		{
			loadedBundle = null;
		}
		else
		{
			loadedBundle = JRResourcesUtil.loadResourceBundle(resourceBundleBaseName, locale);//FIXMECONTEXT check how to pass class loader here
		}
		return loadedBundle;
	}


	/**
	 * Reads built-in parameter values from the value map.
	 * 
	 * @param parameterValues the parameter values
	 * @throws JRException 
	 */
	public void setParameterValues(Map<String,Object> parameterValues) throws JRException
	{
		parameterValues.put(JRParameter.REPORT_PARAMETERS_MAP, parameterValues);
		
		if (filler != null)
		{
			// the only case when this filler is null is when called from JRParameterDefaultValuesEvaluator
			// and that utility method already sets the report object in the map
			parameterValues.put(JRParameter.JASPER_REPORT, filler.getJasperReport());
		}
		
		reportMaxCount = (Integer) parameterValues.get(JRParameter.REPORT_MAX_COUNT);

		locale = (Locale) parameterValues.get(JRParameter.REPORT_LOCALE);
		if (locale == null)
		{
			locale = Locale.getDefault();
			parameterValues.put(JRParameter.REPORT_LOCALE, locale);
		}
		
		resourceBundle = (ResourceBundle) parameterValues.get(JRParameter.REPORT_RESOURCE_BUNDLE);
		if (resourceBundle == null)
		{
			resourceBundle = loadResourceBundle();
			if (resourceBundle != null)
			{
				parameterValues.put(JRParameter.REPORT_RESOURCE_BUNDLE, resourceBundle);
			}
		}
		
		timeZone = (TimeZone) parameterValues.get(JRParameter.REPORT_TIME_ZONE);
		if (timeZone == null)
		{
			timeZone = TimeZone.getDefault();
			parameterValues.put(JRParameter.REPORT_TIME_ZONE, timeZone);
		}
		
		scriptlets = createScriptlets(parameterValues);
		delegateScriptlet.setData(parametersMap, fieldsMap, variablesMap, groups);//FIXMESCRIPTLET use some context

		contributeParameters(parameterValues);
		
		filter = (DatasetFilter) parameterValues.get(JRParameter.FILTER);

		// initializing cache because we need the cached parameter values
		cacheInit();
		
		setFillParameterValues(parameterValues);
		
		// after we have the parameter values, init cache recording
		cacheInitRecording();
		
		// initialize the filter
		if (filter != null)
		{
			filter.init(this);
		}
	}
	
	
	/**
	 * Initializes the data source which will be used by this dataset.
	 * 
	 * If the dataset includes a query, this involves invoking the appropriate
	 * query executer to execute the query and create a data source from the
	 * results.
	 * 
	 * @throws JRException
	 */
	public void initDatasource() throws JRException
	{
		queryExecuter = null;
		dataSource = null;

		if (cachedDataset != null)
		{
			// get the cached data source
			dataSource = cachedDataset.getDataSource();
		}
		
		if (dataSource == null)
		{
			dataSource = (JRDataSource) getParameterValue(JRParameter.REPORT_DATA_SOURCE);
			if (!useDatasourceParamValue && (useConnectionParamValue || dataSource == null))
			{
				dataSource = createQueryDatasource();
				setParameter(JRParameter.REPORT_DATA_SOURCE, dataSource);
			}
		}

		if (DatasetSortUtil.needSorting(this))
		{
			dataSource = DatasetSortUtil.getSortedDataSource(filler, this, locale);
			setParameter(JRParameter.REPORT_DATA_SOURCE, dataSource);
			sortedDataSource = true;
		}
	}

	public void setFillPosition(FillDatasetPosition fillPosition)
	{
		if (fillPosition != null)
		{
			// add default attributes
			fillPosition.addAttribute("datasetUUID", getUUID());
			
			if (query != null)
			{
				MD5Digest queryMD5 = DigestUtils.instance().md5(query.getText());
				fillPosition.addAttribute("queryMD5", queryMD5);
			}
			//FIXME optimize storage for repeating subreports/subdatasets
		}

		this.fillPosition = fillPosition;
	}


	public void setCacheSkipped(boolean cacheSkipped)
	{
		this.cacheSkipped = cacheSkipped;
		
		if (cacheSkipped && log.isDebugEnabled())
		{
			log.debug("data cache skipped at position " + fillPosition);
		}
	}
	
	protected void cacheInit() throws DataSnapshotException
	{
		// resetting
		cachedDataset = null;
		dataRecorder = null;
		
		if (fillPosition == null)
		{
			log.debug("No fill position present, not using the data cache");
			return;
		}
		
		if (filler != null)
		{
			// try to load a cached data snapshot
			cacheInitSnapshot();
		}
	}
	
	protected void cacheInitSnapshot() throws DataSnapshotException
	{
		if (cacheSkipped)
		{
			return;
		}

		DataSnapshot dataSnapshot = filler.fillContext.getDataSnapshot();
		if (dataSnapshot != null)
		{
			// using cached data
			cachedDataset = dataSnapshot.getCachedData(fillPosition);
			if (cachedDataset == null)
			{
				throw new DataSnapshotException("No snapshot data found for position " + fillPosition);
			}
			
			if (log.isDebugEnabled())
			{
				log.debug("Using cached data for " + fillPosition);
			}
		}
	}

	protected void cacheInitRecording()
	{
		if (cacheSkipped)
		{
			return;
		}

		if (cachedDataset != null)
		{
			// we have a cache dataset, nothing to do
			return;
		}
		
		if (filler == null)
		{
			// not a regular report fill, nothing to do
			return;
		}
		
		// if no cached data snapshot, initialize data recording
		DataRecorder cacheRecorder = filler.fillContext.getDataRecorder();
		if (cacheRecorder != null && cacheRecorder.isEnabled())
		{
			// see if data recording is inhibited
			boolean dataRecorable = JRPropertiesUtil.getInstance(getJasperReportsContext()).getBooleanProperty(
					this, DataCacheHandler.PROPERTY_DATA_RECORDABLE, true);
			if (dataRecorable)
			{
				// check whether the data snapshot can be persisted
				boolean dataPersistable = JRPropertiesUtil.getInstance(getJasperReportsContext()).getBooleanProperty(
						this, DataCacheHandler.PROPERTY_DATA_PERSISTABLE, true);
				if (!dataPersistable)
				{
					if (log.isDebugEnabled())
					{
						log.debug("data not persistable by property for " + fillPosition);
					}
					
					cacheRecorder.disablePersistence();
				}
				
				// populating the cache
				dataRecorder = cacheRecorder.createRecorder();
				// this will also remove existing data on rewind
				dataRecorder.start(parent.getFields());
				
				if (log.isDebugEnabled())
				{
					log.debug("Populating data cache for " + fillPosition);
				}
				
				// storing persisted parameter values
				for (JRFillParameter parameter : parameters)
				{
					if (parameter.hasProperties())
					{
						boolean includedInCache = isIncludedInDataCache(parameter);
						if (includedInCache)
						{
							if (log.isDebugEnabled())
							{
								log.debug("storing value of paramter " + parameter.getName() 
										+ " in data snapshot");
							}
							
							Object value = parameter.getValue();
							// we store nulls as well
							dataRecorder.addParameter(parameter.getName(), value);
						}
					}
				}
				
				cacheRecordIndexCallbacks = new HashMap<Integer, CacheRecordIndexCallback>();
			}
			else
			{
				if (log.isDebugEnabled())
				{
					log.debug("data recording inhibited by property for " + fillPosition);
				}
				
				// disable all recording
				cacheRecorder.disableRecording();
			}
		}
	}

	protected boolean isIncludedInDataCache(JRFillParameter parameter)
	{
		String includedProp = JRPropertiesUtil.getOwnProperty(parameter, DataCacheHandler.PROPERTY_INCLUDED); 
		return JRPropertiesUtil.asBoolean(includedProp);
	}

	protected void cacheRecord()
	{
		if (dataRecorder != null && !dataRecorder.hasEnded())
		{
			Object[] values;
			if (fields == null)
			{
				values = new Object[0];
			}
			else
			{
				values = new Object[fields.length];
				for (int i = 0; i < fields.length; i++)
				{
					values[i] = fields[i].getValue();
				}
			}
			
			dataRecorder.addRecord(values);
		}
	}

	protected void cacheEnd()
	{
		if (dataRecorder != null && !dataRecorder.hasEnded())
		{
			// if we had a sorted data source, compute the original filtered record indexes
			if (sortedDataSource)
			{
				if (log.isDebugEnabled())
				{
					log.debug("populating unsorted cache");
				}
				
				int recordIndex = 0;
				// ugly cast
				List<SortedDataSource.SortRecord> sortRecords = ((SortedDataSource) dataSource).getRecords();
				for (SortedDataSource.SortRecord sortRecord : sortRecords)
				{
					if (sortRecord.isFiltered())
					{
						// add the record to the data snapshot
						dataRecorder.addRecord(sortRecord.getValues());
						
						// current unsorted index
						++recordIndex;
						int originalIndex = sortRecord.getRecordIndex() + 1;
						
						if (log.isDebugEnabled())
						{
							log.debug("unsorted index " + recordIndex + " for original index " + originalIndex);
						}
						
						// call delayed record index callbacks
						CacheRecordIndexCallback recordIndexCallback = cacheRecordIndexCallbacks.get(originalIndex);
						if (recordIndexCallback != null)
						{
							recordIndexCallback.cacheRecordIndexAvailable(recordIndex);
						}
					}
				}
			}
			
			Object recorded = dataRecorder.end();
			if (recorded != null)
			{
				// adding the recorded data to a temporary list because the fill position might not be final
				filler.fillContext.addDataRecordResult(fillPosition, recorded);
			}
		}
	}

	/**
	 * Sets the parameter values from the values map.
	 * 
	 * @param parameterValues the values map
	 * @throws JRException
	 */
	private void setFillParameterValues(Map<String,Object> parameterValues) throws JRException
	{
		if (parameters != null && parameters.length > 0)
		{
			for (int i = 0; i < parameters.length; i++)
			{
				JRFillParameter parameter = parameters[i];
				String paramName = parameter.getName();
				
				Object value = null;
				if (parameterValues.containsKey(paramName))
				{
					value = parameterValues.get(paramName);
				}
				else if (!parameter.isSystemDefined())
				{
					if (isIncludedInDataCache(parameter) && cachedDataset != null)
					{
						// if it's a cache parameter and we have a cached dataset,
						// look for the value in the cache
						if (!cachedDataset.hasParameter(paramName))
						{
							// cached data is invalid
							throw new DataSnapshotException("A value for parameter " + paramName 
									+ " was not found in the data snapshot");
						}
						
						if (log.isDebugEnabled())
						{
							log.debug("loading parameter " + paramName + " value from data snapshot");
						}
						
						value = cachedDataset.getParameterValue(paramName);
					}
					else
					{
						value = calculator.evaluate(parameter.getDefaultValueExpression(), JRExpression.EVALUATION_DEFAULT);
						if (value != null)
						{
							parameterValues.put(paramName, value);
						}
					}
				}
				setParameter(parameter, value);
			}
		}
	}


	/**
	 * 
	 */
	public void contributeParameters(Map<String,Object> parameterValues) throws JRException
	{
		parameterContributors = getParameterContributors(new ParameterContributorContext(getJasperReportsContext(), this, parameterValues));
		if (parameterContributors != null)
		{
			for(ParameterContributor contributor : parameterContributors)
			{
				contributor.contributeParameters(parameterValues);
			}
		}
	}

	public void setJasperReportsContext(JasperReportsContext jasperReportsContext)
	{
		this.jasperReportsContext = jasperReportsContext;
	}
	
	protected JasperReportsContext getJasperReportsContext()
	{
		return filler == null
				? (jasperReportsContext == null ? DefaultJasperReportsContext.getInstance() : jasperReportsContext)
				: filler.getJasperReportsContext();
	}
	
	/**
	 * 
	 */
	public void disposeParameterContributors()
	{
		if (parameterContributors != null)
		{
			for(ParameterContributor contributor : parameterContributors)
			{
				contributor.dispose();
			}
		}
	}

	
	/**
	 *
	 */
	private List<ParameterContributor> getParameterContributors(ParameterContributorContext context) throws JRException
	{
		List<ParameterContributor> allContributors = null;
		List<?> factories = getJasperReportsContext().getExtensions(ParameterContributorFactory.class);
		if (factories != null && factories.size() > 0)
		{
			allContributors = new ArrayList<ParameterContributor>();
			for (Iterator<?> it = factories.iterator(); it.hasNext();)
			{
				ParameterContributorFactory factory = (ParameterContributorFactory)it.next();
				List<ParameterContributor> contributors = factory.getContributors(context);
				if (contributors != null)
				{
					allContributors.addAll(contributors);
				}
			}
		}
		return allContributors;
	}

	
	/**
	 * Returns the map of parameter values.
	 * 
	 * @return the map of parameter values
	 */
	protected Map<String,Object> getParameterValuesMap()
	{
		JRFillParameter paramValuesParameter = parametersMap.get(JRParameter.REPORT_PARAMETERS_MAP);
		return (Map<String,Object>) paramValuesParameter.getValue();
	}
	
	/**
	 * Creates the data source from a connection.
	 * 
	 * @return the data source to be used
	 * @throws JRException
	 */
	private JRDataSource createQueryDatasource() throws JRException
	{
		if (query == null)
		{
			return null;
		}

		try
		{
			if (log.isDebugEnabled())
			{
				log.debug("Fill " + filler.fillerId + ": Creating " + query.getLanguage() + " query executer");
			}
			
			QueryExecuterFactory queryExecuterFactory = JRQueryExecuterUtils.getInstance(getJasperReportsContext()).getExecuterFactory(query.getLanguage());
			queryExecuter = queryExecuterFactory.createQueryExecuter(getJasperReportsContext(), parent, parametersMap);
			filler.fillContext.setRunningQueryExecuter(queryExecuter);
			
			return queryExecuter.createDatasource();
		}
		finally
		{
			filler.fillContext.clearRunningQueryExecuter();
		}
	}


	protected void reset()
	{
		useDatasourceParamValue = false;
		useConnectionParamValue = false;
	}

	
	/**
	 * Sets the data source to be used.
	 * 
	 * @param parameterValues the parameter values
	 * @param ds the data source
	 */
	public void setDatasourceParameterValue(Map<String,Object> parameterValues, JRDataSource ds)
	{
		useDatasourceParamValue = true;
		
		if (ds != null)
		{
			parameterValues.put(JRParameter.REPORT_DATA_SOURCE, ds);
		}
	}


	/**
	 * Sets the JDBC connection to be used.
	 * 
	 * @param parameterValues the parameter values
	 * @param conn the connection
	 */
	public void setConnectionParameterValue(Map<String,Object> parameterValues, Connection conn)
	{
		useConnectionParamValue = true;
		
		if (conn != null)
		{
			parameterValues.put(JRParameter.REPORT_CONNECTION, conn);
		}
	}
	
	
	/**
	 * Closes the data source used by this dataset if this data source was
	 * obtained via a query executer.
	 * 
	 * @see JRQueryExecuter#close()
	 */
	public void closeDatasource()
	{
		closeQueryExecuter();
		reset();

		if (ended)
		{
			// if the whole data source was iterated, submit the recorded data
			cacheEnd();
		}
	}

	protected void closeQueryExecuter()
	{
		if (queryExecuter != null)
		{
			if (log.isDebugEnabled())
			{
				log.debug("Fill " + filler.fillerId + ": closing query executer");
			}

			queryExecuter.close();
			queryExecuter = null;
		}
	}

	
	/**
	 * Starts the iteration on the data source.
	 */
	public void start()
	{
		// resetting the variables is required for cases such as sort fields which
		// iterate and calculate variables.
		resetVariables();
		
		reportCount = 0;
		ended = false;
		
		cacheRecordCount = 0;
		previousCacheRecordIndex = 0;
		currentCacheRecordIndex = 0;
	}

	
	protected void resetVariables()
	{
		if (variables != null)
		{
			for (JRFillVariable variable : variables)
			{
				variable.reset();
			}
		}
	}


	/**
	 * Moves to the next record in the data source.
	 * 
	 * @return <code>true</code> if the data source was not exhausted
	 * @throws JRException
	 */
	public boolean next() throws JRException
	{
		return next(false);
	}


	/**
	 * Moves to the next record in the data source.
	 * 
	 * @param sorting whether the method is called as part of the data sorting phase
	 * @return <code>true</code> if the data source was not exhausted
	 * @throws JRException
	 */
	protected boolean next(boolean sorting) throws JRException
	{
		boolean hasNext = false;

		if (dataSource != null)
		{
			boolean includeRow = true;
			do
			{
				// limits are applied after sorting to support top-N reports
				hasNext = advanceDataSource(!sorting);
				if (hasNext)
				{
					setOldValues();

					calculator.estimateVariables();
					
					// filters are applied after sorting to support top-N reports
					if (!sorting)
					{
						includeRow = true;
						
						// evaluate the filter expression
						JRExpression filterExpression = getFilterExpression();
						if (filterExpression != null)
						{
							Boolean filterExprResult = (Boolean) calculator.evaluate(
									filterExpression, JRExpression.EVALUATION_ESTIMATED);
							includeRow = filterExprResult != null && filterExprResult.booleanValue();
						}

						if (includeRow)
						{
							advanceCacheRecordIndexes();
							
							if (sortedDataSource)
							{
								// mark the record as filtered in the sorted data source
								// ugly cast
								((SortedDataSource) dataSource).setRecordFilteredIndex(cacheRecordCount - 1);
							}
							else
							{
								// cache the record if the filter expression evaluated to true;
								// dynamic filters do not exclude records from the cache.
								// sorted data source cache records at the end because they compute original indexes.
								cacheRecord();
							}
							
							if (filter != null)
							{
								includeRow = filter.matches(EvaluationType.ESTIMATED);
								if (log.isDebugEnabled())
								{
									log.debug("Record matched by filter: " + includeRow);
								}
							}
						}
					}

					if (!includeRow)
					{
						revertToOldValues();
					}
				}
			}
			while(hasNext && !includeRow);
			
			if (hasNext)
			{
				++reportCount;
			}
		}
		
		if (!hasNext)
		{
			ended = true;
		}

		return hasNext;
	}

	protected void advanceCacheRecordIndexes()
	{
		++cacheRecordCount;
		previousCacheRecordIndex = currentCacheRecordIndex;
		
		// if we're using a cached data source, return the original record index
		// this covers both sorting a cached data source and filtering a cached data source
		if (cachedDataset != null)
		{
			// ugly cast
			int dataSourceIndex = ((IndexedDataSource) dataSource).getRecordIndex();
			// indexes are 1-based
			currentCacheRecordIndex = dataSourceIndex + 1;
		}
		else
		{
			currentCacheRecordIndex = cacheRecordCount;
		}
	}


	protected void setOldValues() throws JRException
	{
		if (fields != null && fields.length > 0)
		{
			for (int i = 0; i < fields.length; i++)
			{
				JRFillField field = fields[i];
				field.setPreviousOldValue(field.getOldValue());
				field.setOldValue(field.getValue());
				field.setValue(dataSource.getFieldValue(field));
			}
		}

		if (variables != null && variables.length > 0)
		{
			for (int i = 0; i < variables.length; i++)
			{
				JRFillVariable variable = variables[i];
				variable.setPreviousOldValue(variable.getOldValue());
				variable.setOldValue(variable.getValue());
			}
		}
	}


	protected void revertToOldValues()
	{
		if (fields != null && fields.length > 0)
		{
			for (int i = 0; i < fields.length; i++)
			{
				JRFillField field = fields[i];
				field.setValue(field.getOldValue());
				field.setOldValue(field.getPreviousOldValue());
			}
		}
		
		if (variables != null && variables.length > 0)
		{
			for (int i = 0; i < variables.length; i++)
			{
				JRFillVariable variable = variables[i];
				variable.setValue(variable.getOldValue());
				variable.setOldValue(variable.getPreviousOldValue());
			}
		}
	}


	protected boolean advanceDataSource(boolean limit) throws JRException
	{
		boolean hasNext;
		if (limit && reportMaxCount != null && reportCount >= reportMaxCount.intValue())
		{
			hasNext = false;
		}
		else
		{
			hasNext = dataSource.next();
		}
		return hasNext;
	}
	
	/**
	 * Sets the value of a parameter.
	 * 
	 * @param parameterName the parameter name
	 * @param value the value
	 * @throws JRException
	 */
	protected void setParameter(String parameterName, Object value) throws JRException
	{
		JRFillParameter parameter = parametersMap.get(parameterName);
		if (parameter != null)
		{
			setParameter(parameter, value);
		}
	}
	
	
	/**
	 * Sets the value of the parameter.
	 * 
	 * @param parameter the parameter
	 * @param value the value
	 * @throws JRException
	 */
	protected void setParameter(JRFillParameter parameter, Object value) throws JRException
	{
//		if (value != null)
//		{
//			if (parameter.getValueClass().isInstance(value))
//			{
//				parameter.setValue(value);
//			}
//			else
//			{
//				throw new JRException(
//					"Incompatible " 
//					+ value.getClass().getName() 
//					+ " value assigned to parameter " 
//					+ parameter.getName() 
//					+ " in the " + getName() + " dataset."
//					);
//			}
//		}
//		else
//		{
			parameter.setValue(value);
//		}
	}

	
	/**
	 * Returns the value of a variable.
	 * 
	 * @param variableName the variable name
	 * @return the variable value
	 */
	public Object getVariableValue(String variableName)
	{
		return getVariableValue(variableName, EvaluationType.DEFAULT);
	}

	public Object getVariableValue(String variableName, EvaluationType evaluation)
	{
		JRFillVariable var = variablesMap.get(variableName);
		if (var == null)
		{
			throw new JRRuntimeException("No such variable " + variableName);
		}
		return var.getValue(evaluation.getType());
	}

	public JRFillVariable getFillVariable(String variableName)
	{
		return variablesMap.get(variableName);
	}
	
	/**
	 * Returns the value of a parameter.
	 * 
	 * @param parameterName the parameter name
	 * @return the parameter value
	 */
	public Object getParameterValue(String parameterName)
	{
		return getParameterValue(parameterName, false);
	}

	
	/**
	 * Returns the value of a parameter.
	 * 
	 * @param parameterName the parameter name
	 * @param ignoreMissing if set, <code>null</code> will be returned for inexisting parameters
	 * @return the parameter value
	 */
	public Object getParameterValue(String parameterName, boolean ignoreMissing)
	{
		JRFillParameter param = parametersMap.get(parameterName);
		Object value;
		if (param == null)
		{
			if (!ignoreMissing)
			{
				throw new JRRuntimeException("No such parameter " + parameterName);
			}
			
			// look into REPORT_PARAMETERS_MAP
			Map<String, Object> valuesMap = getParameterValuesMap();
			value = valuesMap == null ? null : valuesMap.get(parameterName);
		}
		else
		{
			value = param.getValue();
		}
		return value;
	}

	
	/**
	 * Returns the value of a field.
	 * 
	 * @param fieldName the field name
	 * @return the field value
	 */
	public Object getFieldValue(String fieldName)
	{
		return getFieldValue(fieldName, EvaluationType.DEFAULT);
	}
	
	public Object getFieldValue(String fieldName, EvaluationType evaluation)
	{
		JRFillField field = fieldsMap.get(fieldName);
		if (field == null)
		{
			throw new JRRuntimeException("No such field " + fieldName);
		}
		return field.getValue(evaluation.getType());
	}
	
	public JRFillField getFillField(String fieldName)
	{
		return fieldsMap.get(fieldName);
	}
	
	/**
	 * Class used to hold expression calculation  requirements.
	 */
	protected static class VariableCalculationReq
	{
		String variableName;

		CalculationEnum calculation;

		VariableCalculationReq(String variableName, CalculationEnum calculation)
		{
			this.variableName = variableName;
			this.calculation = calculation;
		}

		public boolean equals(Object o)
		{
			if (o == null || !(o instanceof VariableCalculationReq))
			{
				return false;
			}

			VariableCalculationReq r = (VariableCalculationReq) o;

			return variableName.equals(r.variableName) && calculation == r.calculation;
		}

		public int hashCode()
		{
			return 31 * calculation.getValue() + variableName.hashCode();
		}
	}
	
	
	/**
	 * Adds a variable calculation requirement.
	 * 
	 * @param variableName the variable name
	 * @param calculation the required calculation
	 */
	protected void addVariableCalculationReq(String variableName, CalculationEnum calculation)
	{
		if (variableCalculationReqs == null)
		{
			variableCalculationReqs = new HashSet<VariableCalculationReq>();
		}

		variableCalculationReqs.add(new VariableCalculationReq(variableName, calculation));
	}

	
	/**
	 * Checks if there are variable calculation requirements and creates the required variables.
	 * 
	 * @param factory the fill object factory
	 */
	protected void checkVariableCalculationReqs(JRFillObjectFactory factory)
	{
		if (variableCalculationReqs != null && !variableCalculationReqs.isEmpty())
		{
			List<JRFillVariable> variableList = new ArrayList<JRFillVariable>(variables.length * 2);

			for (int i = 0; i < variables.length; i++)
			{
				JRFillVariable variable = variables[i];
				checkVariableCalculationReq(variable, variableList, factory);
			}

			setVariables(variableList);
		}
	}

	
	private void checkVariableCalculationReq(JRFillVariable variable, List<JRFillVariable> variableList, JRFillObjectFactory factory)
	{
		if (hasVariableCalculationReq(variable, CalculationEnum.AVERAGE) || hasVariableCalculationReq(variable, CalculationEnum.VARIANCE))
		{
			if (variable.getHelperVariable(JRCalculable.HELPER_COUNT) == null)
			{
				JRVariable countVar = createHelperVariable(variable, "_COUNT", CalculationEnum.COUNT);
				JRFillVariable fillCountVar = factory.getVariable(countVar);
				checkVariableCalculationReq(fillCountVar, variableList, factory);
				variable.setHelperVariable(fillCountVar, JRCalculable.HELPER_COUNT);
			}

			if (variable.getHelperVariable(JRCalculable.HELPER_SUM) == null)
			{
				JRVariable sumVar = createHelperVariable(variable, "_SUM", CalculationEnum.SUM);
				JRFillVariable fillSumVar = factory.getVariable(sumVar);
				checkVariableCalculationReq(fillSumVar, variableList, factory);
				variable.setHelperVariable(fillSumVar, JRCalculable.HELPER_SUM);
			}
		}

		if (hasVariableCalculationReq(variable, CalculationEnum.STANDARD_DEVIATION))
		{
			if (variable.getHelperVariable(JRCalculable.HELPER_VARIANCE) == null)
			{
				JRVariable varianceVar = createHelperVariable(variable, "_VARIANCE", CalculationEnum.VARIANCE);
				JRFillVariable fillVarianceVar = factory.getVariable(varianceVar);
				checkVariableCalculationReq(fillVarianceVar, variableList, factory);
				variable.setHelperVariable(fillVarianceVar, JRCalculable.HELPER_VARIANCE);
			}
		}

		if (hasVariableCalculationReq(variable, CalculationEnum.DISTINCT_COUNT))
		{
			if (variable.getHelperVariable(JRCalculable.HELPER_COUNT) == null)
			{
				JRVariable countVar = createDistinctCountHelperVariable(variable);
				JRFillVariable fillCountVar = factory.getVariable(countVar);
				checkVariableCalculationReq(fillCountVar, variableList, factory);
				variable.setHelperVariable(fillCountVar, JRCalculable.HELPER_COUNT);
			}
		}

		variableList.add(variable);
	}

	
	private boolean hasVariableCalculationReq(JRVariable var, CalculationEnum calculation)
	{
		return variableCalculationReqs.contains(new VariableCalculationReq(var.getName(), calculation));
	}

	@Override
	public UUID getUUID()
	{
		return parent.getUUID();
	}

	public String getName()
	{
		return parent.getName();
	}

	public String getScriptletClass()
	{
		return parent.getScriptletClass();
	}

	public JRScriptlet[] getScriptlets()
	{
		return parent.getScriptlets();
	}

	public JRParameter[] getParameters()
	{
		return parameters;
	}

	public Map<String,JRFillParameter> getParametersMap()
	{
		return parametersMap;
	}

	public JRQuery getQuery()
	{
		return query;
	}

	public JRField[] getFields()
	{
		return fields;
	}

	public JRSortField[] getSortFields()
	{
		return parent.getSortFields();
	}

	public JRVariable[] getVariables()
	{
		return variables;
	}

	public JRGroup[] getGroups()
	{
		return groups;
	}

	public boolean isMainDataset()
	{
		return isMain;
	}

	public String getResourceBundle()
	{
		return parent.getResourceBundle();
	}


	public WhenResourceMissingTypeEnum getWhenResourceMissingTypeValue()
	{
		return whenResourceMissingType;
	}


	public void setWhenResourceMissingType(WhenResourceMissingTypeEnum whenResourceMissingType)
	{
		this.whenResourceMissingType = whenResourceMissingType;
	}

	
	public boolean hasProperties()
	{
		return parent.hasProperties();
	}


	public JRPropertiesMap getPropertiesMap()
	{
		return parent.getPropertiesMap();
	}

	
	public JRPropertiesHolder getParentProperties()
	{
		// report properties propagate to subdatasets
		return isMain ? null : filler.getJasperReport();
	}


	public JRExpression getFilterExpression()
	{
		return parent.getFilterExpression();
	}
	
	/**
	 *
	 */
	public Object clone() 
	{
		throw new UnsupportedOperationException();
	}

	/**
	 * Evaluates an expression
	 * @param expression the expression
	 * @param evaluation the evaluation type
	 * @return the evaluation result
	 * @throws JRException
	 */
	public Object evaluateExpression(JRExpression expression, byte evaluation) throws JRException
	{
		return calculator.evaluate(expression, evaluation);
	}
	
	public Locale getLocale()
	{
		return locale;
	}

	public FillDatasetPosition getDatasetPosition()
	{
		return fillPosition;
	}
	
	protected static interface CacheRecordIndexCallback
	{
		void cacheRecordIndexAvailable(int recordIndex);
	}
	
	protected static class FillDatasetPositionRecordIndexCallback implements CacheRecordIndexCallback
	{
		protected static void setRecordIndex(FillDatasetPosition position, int recordIndex)
		{
			position.addAttribute("rowIndex", recordIndex);
		}
		
		private final FillDatasetPosition position;

		public FillDatasetPositionRecordIndexCallback(FillDatasetPosition position)
		{
			this.position = position;
		}

		@Override
		public void cacheRecordIndexAvailable(int recordIndex)
		{
			setRecordIndex(position, recordIndex);
		}
	}
	
	protected static class CacheRecordIndexChainedCallback implements CacheRecordIndexCallback
	{
		private final CacheRecordIndexCallback first;
		private final CacheRecordIndexCallback second;

		public CacheRecordIndexChainedCallback(CacheRecordIndexCallback first,
				CacheRecordIndexCallback second)
		{
			this.first = first;
			this.second = second;
		}

		@Override
		public void cacheRecordIndexAvailable(int recordIndex)
		{
			first.cacheRecordIndexAvailable(recordIndex);
			second.cacheRecordIndexAvailable(recordIndex);
		}
	}
	
	protected void addCacheRecordIndexCallback(int recordIndex, CacheRecordIndexCallback callback)
	{
		CacheRecordIndexCallback existingCallback = cacheRecordIndexCallbacks.get(recordIndex);
		if (existingCallback == null)
		{
			cacheRecordIndexCallbacks.put(recordIndex, callback);
		}
		else
		{
			CacheRecordIndexChainedCallback chainedCallback = new CacheRecordIndexChainedCallback(
					existingCallback, callback);
			cacheRecordIndexCallbacks.put(recordIndex, chainedCallback);
		}
	}
	
	public void setCacheRecordIndex(FillDatasetPosition position, byte evaluationType)
	{
		int recordIndex;
		switch (evaluationType)
		{
		case JRExpression.EVALUATION_OLD:
			recordIndex = previousCacheRecordIndex;
			break;
		default:
			recordIndex = currentCacheRecordIndex;
			break;
		}
		
		if (sortedDataSource && dataRecorder != null)
		{
			// when recording a sorted data source, the record indexes are computed at the end
			// and we need to store a callback
			FillDatasetPositionRecordIndexCallback callback = new FillDatasetPositionRecordIndexCallback(position);
			addCacheRecordIndexCallback(recordIndex, callback);
			
			if (log.isDebugEnabled())
			{
				log.debug("registered cache callback for index " + recordIndex);
			}
		}
		else
		{
			FillDatasetPositionRecordIndexCallback.setRecordIndex(position, recordIndex);
		}
	}
}
